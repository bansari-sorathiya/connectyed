<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Meeting;
use App\Models\User;
use Carbon\Carbon;
use App\Mail\SendCouponCode;
use App\Mail\PurchaseConfirmation;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    /**
     * Initialize Stripe API key.
     */
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Handle incoming Stripe webhook events.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('stripe-signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        Log::info('Webhook received', [
            'headers' => $request->headers->all(),
            'has_signature' => !empty($sig_header),
            'has_secret' => !empty($webhookSecret)
        ]);

        try {
            if (empty($webhookSecret)) {
                throw new \Exception('Webhook secret not configured');
            }

            $event = Webhook::constructEvent(
                $payload, 
                $sig_header, 
                $webhookSecret
            );

            Log::info('Webhook event constructed', [
                'type' => $event->type,
                'id' => $event->id
            ]);

            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;
                
                Log::info('Processing checkout session', [
                    'session_id' => $session->id,
                    'metadata' => $session->metadata ?? null
                ]);

                // Delegate to specific handlers based on metadata
                return $this->handleCheckoutSessionCompleted($session);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Webhook handling failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Handle checkout.session.completed event
     */
    private function handleCheckoutSessionCompleted($session)
    {
        $metadata = $session->metadata;
        
        Log::info('Processing checkout session', [
            'session_id' => $session->id,
            'metadata' => $metadata
        ]);

        if (isset($metadata->meeting_id)) {
            return $this->handleMeetingPayment($session);
        } 
        
        if (isset($metadata->client_id, $metadata->product_type)) {
            return $this->handleProductPurchase($session);
        }

        Log::warning('Unhandled checkout session', ['metadata' => $metadata]);
        return response()->json(['status' => 'unhandled_event'], 200);
    }

    /**
     * Handle product purchases
     */
    private function handleProductPurchase($session)
    {
        $clientId = $session->metadata->client_id;
        $productType = $session->metadata->product_type;
        $isFirstPurchase = filter_var($session->metadata->is_first_purchase ?? 'false', FILTER_VALIDATE_BOOLEAN);

        Log::info('Processing purchase', [
            'client_id' => $clientId,
            'product_type' => $productType,
            'is_first_purchase' => $isFirstPurchase,
            'session_id' => $session->id
        ]);

        DB::beginTransaction();

        try {
            $client = User::findOrFail($clientId);
            $wasFirstPurchase = !$client->package_purchased_at;

            $packageMap = [
                'silver_package' => [
                    'criteria_limit' => 4,
                    'package_name' => 'silver'
                ],
                'gold_package' => [
                    'criteria_limit' => 6,
                    'package_name' => 'gold'
                ],
                'platinum_package' => [
                    'criteria_limit' => 9,
                    'package_name' => 'platinum'
                ]
            ];

            if (!isset($packageMap[$productType])) {
                throw new \Exception("Invalid product type: {$productType}");
            }

            $packageDetails = $packageMap[$productType];

            // Update user package
            $updateData = [
                'purchased_package' => $packageDetails['package_name'],
                'criteria_limit' => $packageDetails['criteria_limit'],
                'package_purchased_at' => $wasFirstPurchase ? now() : $client->package_purchased_at
            ];

            // Only initialize empty criteria on first purchase
            if ($wasFirstPurchase) {
                $updateData['criteria'] = json_encode([]);
            }

            $updated = $client->update($updateData);

            if (!$updated) {
                throw new \Exception("Failed to update user package");
            }

            Log::info('Package updated successfully', [
                'client_id' => $clientId,
                'package' => $packageDetails['package_name'],
                'was_first_purchase' => $wasFirstPurchase
            ]);

            // Send confirmation email
            try {
                $emailData = [
                    'isFirstPurchase' => $wasFirstPurchase,
                    'package' => $packageDetails['package_name'],
                    'criteriaLimit' => $packageDetails['criteria_limit']
                ];
                
                Mail::to($client->email)
                    ->send(new PurchaseConfirmation($client, $productType, $emailData));
                
                Log::info('Confirmation email sent', ['email' => $client->email]);
            } catch (\Exception $e) {
                Log::error('Email sending failed', [
                    'error' => $e->getMessage(),
                    'client_id' => $clientId
                ]);
            }

            DB::commit();
            
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'client_id' => $clientId,
                'session_id' => $session->id
            ]);
            throw $e;
        }
    }

    /**
     * Handle meeting payment completion
     */
    private function handleMeetingPayment($session)
    {
        $meetingId = $session->metadata->meeting_id;

        Log::info('Processing meeting payment', ['meeting_id' => $meetingId]);

        DB::beginTransaction();

        try {
            $meeting = Meeting::with(['clients', 'matchmaker'])->findOrFail($meetingId);
            $matchmaker = $meeting->matchmaker;

            // Handle Google Calendar integration
            $googleEvent = $this->createGoogleMeetEvent($meeting, $matchmaker);

            // Update meeting details
            $amountTotal = $session->amount_total / 100;
            $meeting->update([
                'google_meet_id' => $googleEvent->getId(),
                'google_meet_link' => $googleEvent->getHangoutLink(),
                'status' => 'confirmed',
                'amount' => $amountTotal,
                'matchmaker_earnings' => $amountTotal * 0.35,
                'admin_earnings' => $amountTotal * 0.50,
                'client_credit' => $meeting->clients->first()->profile->gender === 'female' ? $amountTotal * 0.15 : 0
            ]);

            // Send coupon code outside transaction
            $client = $meeting->clients->first();
            if ($client) {
                try {
                    Mail::to($client->email)->send(new SendCouponCode($client, 'FIRSTSESSION25'));
                    Log::info('Coupon code sent', ['client_id' => $client->id]);
                } catch (\Exception $e) {
                    Log::error('Failed to send coupon', ['error' => $e->getMessage()]);
                }
            }

            DB::commit();
            Log::info('Meeting payment processed', ['meeting_id' => $meetingId]);
            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Meeting payment failed', [
                'error' => $e->getMessage(),
                'meeting_id' => $meetingId
            ]);
            throw $e;
        }
    }

    /**
     * Create Google Meet event
     */
    private function createGoogleMeetEvent($meeting, $matchmaker)
    {
        if (Carbon::now()->gte($matchmaker->google_token_expires_at)) {
            $this->refreshMatchmakerGoogleToken($matchmaker);
        }

        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setAccessToken([
            'access_token' => $matchmaker->google_access_token,
            'refresh_token' => $matchmaker->google_refresh_token,
            'expires_in' => Carbon::parse($matchmaker->google_token_expires_at)->timestamp - now()->timestamp,
            'created' => now()->timestamp
        ]);

        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $newToken = $client->getAccessToken();
            
            $matchmaker->update([
                'google_access_token' => $newToken['access_token'],
                'google_refresh_token' => $newToken['refresh_token'] ?? $matchmaker->google_refresh_token,
                'google_token_expires_at' => Carbon::now()->addSeconds($newToken['expires_in'])
            ]);
        }

        $service = new Google_Service_Calendar($client);
        $startTime = Carbon::parse($meeting->start_time);
        $endTime = $startTime->copy()->addMinutes($meeting->duration);

        $event = new Google_Service_Calendar_Event([
            'summary' => 'Connectyed - One on One Session',
            'start' => [
                'dateTime' => $startTime->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'end' => [
                'dateTime' => $endTime->toRfc3339String(),
                'timeZone' => config('app.timezone'),
            ],
            'conferenceData' => [
                'createRequest' => [
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                    'requestId' => 'meet_' . uniqid(),
                ],
            ],
            'attendees' => $meeting->clients->map(function($client) {
                return ['email' => $client->email];
            })->toArray()
        ]);

        return $service->events->insert('primary', $event, ['conferenceDataVersion' => 1]);
    }

    /**
     * Refresh the matchmaker's Google token
     */
    private function refreshMatchmakerGoogleToken($matchmaker)
    {
        if (!$matchmaker?->google_refresh_token) {
            throw new \Exception('No refresh token available');
        }

        try {
            $client = new Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setAccessType('offline');

            $client->fetchAccessTokenWithRefreshToken($matchmaker->google_refresh_token);
            $newToken = $client->getAccessToken();

            if (!isset($newToken['access_token'])) {
                throw new \Exception('Failed to refresh Google token');
            }

            $matchmaker->update([
                'google_access_token' => $newToken['access_token'],
                'google_refresh_token' => $newToken['refresh_token'] ?? $matchmaker->google_refresh_token,
                'google_token_expires_at' => Carbon::now()->addSeconds($newToken['expires_in'])
            ]);

            Log::info('Google token refreshed', ['matchmaker_id' => $matchmaker->id]);

        } catch (\Exception $e) {
            Log::error('Token refresh failed', [
                'error' => $e->getMessage(),
                'matchmaker_id' => $matchmaker->id
            ]);
            throw $e;
        }
    }
}
