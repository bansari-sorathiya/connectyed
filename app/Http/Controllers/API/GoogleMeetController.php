<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use Google_Client;
use App\Models\User;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use App\Models\Meeting;
use Stripe\StripeClient;
use Google_Service_Calendar;
use Illuminate\Http\Request;
use App\Services\ZoomService;
use Google_Service_Calendar_Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Google_Service_Calendar_EventDateTime;
use Illuminate\Support\Facades\Notification;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Notifications\MeetingPaymentNotification;
use App\Notifications\MeetingScheduledNotification;
use Symfony\Component\HttpFoundation\Session\Session;



class GoogleMeetController extends Controller
{
    protected $stripe;

    public function __construct()
    {
        $this->middleware('auth')->only(['redirectToGoogle', 'handleGoogleCallback']);
        $this->middleware('auth:api')->only(['createMeeting', 'revokeGoogleToken', 'checkGoogleAuthorization', 'getUpcomingMeetings']);
        // Initialize Stripe using configuration
        $stripeSecret = config('services.stripe.secret');

        if (empty($stripeSecret)) {
            Log::error('Stripe secret key is not set in config/services.php');
        } else {
            $this->stripe = new StripeClient($stripeSecret);
        }
    }

    /**
     * Redirect the user to Google's OAuth authorization page.
     */
    public function redirectToGoogle()
    {
        $user = auth('api')->user();

        if (!$user) {
            return redirect('/login');
        }

        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect_uri')); // This should now match your .env

        // Add Scopes
        $client->addScope([
            'openid',
            'profile',
            'email',
            Google_Service_Calendar::CALENDAR_EVENTS,
        ]);

        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();

        return redirect($authUrl);
    }

    /**
     * Handle the OAuth callback from Google and save tokens to the authenticated user.
     */
    public function handleGoogleCallback(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login')->with('error', 'User not authenticated.');
        }

        $code = $request->input('code');

        if (!$code) {
            return redirect('/login')->with('error', 'Authorization code not provided.');
        }

        try {
            $client = new Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect_uri'));

            $token = $client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                Log::error('Google OAuth Error', ['error' => $token['error']]);
                return redirect('/login')->with('error', 'Failed to obtain access token.');
            }

            // Save tokens to the user model
            $user->google_access_token = $token['access_token'];
            if (isset($token['refresh_token'])) {
                $user->google_refresh_token = $token['refresh_token'];
            }
            $user->google_token_expires_at = Carbon::now()->addSeconds($token['expires_in']);
            $user->save(); // Eloquent model's save method

            Log::info('Google tokens saved to user', ['user_id' => $user->id]);

            // Optionally, retrieve and store user profile information
            if (isset($token['id_token'])) {
                $payload = $client->verifyIdToken($token['id_token']);
                if ($payload) {
                    $user->google_id = $payload['sub'];
                    $user->name = $payload['name'] ?? $user->name;
                    $user->picture = $payload['picture'] ?? $user->picture;
                    $user->save(); // Eloquent model's save method
                }
            }

            // Generate JWT token for the user
            $jwtToken = auth('api')->login($user);

            // Determine redirect path based on user role
            if ($user->role === 'matchmaker' || $user->role === 'candidate') {
                $redirectPath = '/matchmaker/communication';
            } elseif ($user->role === 'client') {
                $redirectPath = '/client/communication';
            } else {
                $redirectPath = '/'; // Default redirect
            }

            // Redirect to frontend with token
            return redirect($redirectPath . '?token=' . $jwtToken);
        } catch (\Exception $e) {
            Log::error('Exception during Google OAuth Token Exchange', ['exception' => $e->getMessage()]);
            return redirect('/login')->with('error', 'An error occurred during Google OAuth.');
        }
    }

    /**
     * Create a scheduled Google Meet meeting using the user's access token.
     */
    public function createMeeting(Request $request)
    {
        try{  
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'matchmaker_id' => 'required|exists:users,id',
            'client_ids' => 'required|array|min:1|max:2',
            'start_time' => 'required|date',
            'duration' => 'required|in:15,30,60,120,1440',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed for createMeeting', ['errors' => $validator->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Retrieve authenticated user
        $authUser = Auth::user();

        if (!$authUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please log in.',
            ], 401);
        }
  
        // Prepare Zoom meeting data
        $roomTitle = "Meeting room";
        $roomDuration = $request->duration;
        $roomTiming = Carbon::parse($request->start_time);

        // Generate meeting details
        $data = [
            'title' => $roomTitle,
            'date' => $roomTiming->toISOString(),
            'duration' => $roomDuration,
            'password' => rand(10000, 99999), // Or use a more secure way to generate passwords
            'created_id' => $authUser->id,
            'api_type' => 'global',
            'host_video' => 'enable',
            'join_before_host' => 'enable',
            'client_video' => 'enable',
            'description' => 'This is my first ' . $roomTitle . '.',
            'timezone' => config('app.timezone'),
        ];

        // Create Zoom meeting
        $accessToken = $this->getAccessToken();
        $response = $this->createAMeeting($data, $accessToken);


        if ($response && isset($response->id)) {
            // Save meeting information to the database
            $meetingData = [
                'meeting_topic' => $response->topic,
                'meeting_start_url' => $response->start_url,
                'meeting_join_url' => $response->join_url,
                'google_meet_id' => $response->id,
                'google_meet_link' => $response->join_url,
                'meeting_response' => json_encode($response, JSON_PRETTY_PRINT),
                'google_meet_password' => $response->password,
                'meeting_id' => $response->id,
                'meeting_password' => $data['password'],
                'meeting_start_time' => $roomTiming,
                'meeting_duration' => $roomDuration,
                'created_by' => $authUser->id,
                'matchmaker_id' => $request->matchmaker_id,
                'start_time' => $roomTiming,
                'duration' => $roomDuration,
                'client_id' => $authUser->id
            ];

            if ($authUser->role === 'client') {
                // dd($meetingData);
                // Save meeting record to your database (assuming a Meeting model exists)
                Meeting::create($meetingData);

                $stripeSecret = config('services.stripe.secret');

                if (empty($stripeSecret)) {
                    Log::error('Stripe secret key is not set in config/services.php');
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment configuration error. Please try again later.',
                    ], 500);
                }

                $stripe = new StripeClient($stripeSecret);
            }
            // Determine amount and payment flow based on user role
            if ($authUser->role === 'client') { 
                // Client is scheduling: require payment
                try {
                    $paymentLink = $stripe->paymentLinks->create([
                        'line_items' => [[
                            'price' => 'price_1Q6dsqFZef913bMWGk4etNyd', // Use the specific Price ID
                            'quantity' => 1,
                        ]],
                        'after_completion' => [
                            'type' => 'redirect',
                            'redirect' => ['url' => route('payment.success', ['meeting_id' => $response->id])],
                        ],
                        'metadata' => [
                            'meeting_id' => $response->id,
                        ],
                    ]);

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'start_url' => $response->start_url,
                            'join_url' => $response->join_url,
                            'payment_link' => $paymentLink->url,
                        ],
                        'message' => 'Meeting created successfully. Redirecting to payment...',
                    ], 201);
                } catch (\Exception $e) {
                    Log::error('Error creating Stripe payment link', ['error' => $e->getMessage()]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Error creating payment link'.$e->getMessage(),
                    ], 500);
                }
            } else {
                // For matchmaker: create a meeting entry for each client
                $clients = User::whereIn('id', $request->client_ids)->get();
                foreach ($clients as $client) {
                    $clientMeetingData = array_merge($meetingData, [
                        'client_id' => $client->id,
                        'status' => 'confirmed',
                    ]);

                    // Save meeting for each client and notify them
                    $newMeeting = Meeting::create($clientMeetingData);
                    Notification::send($client, new MeetingScheduledNotification($newMeeting));
                }

                // Notify the matchmaker
                $authUser->notify(new MeetingScheduledNotification($newMeeting));
                return response()->json(['status'=> true , 'message' => "Meeting Scheduled Successfully. Please Check Your Inbox For JOINING LINK"]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create meeting on Zoom.',
            ], 500);
        }

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['status' => 500 , 'message' => "Something went wrong"]);
        }
    }

    public function getAccessToken() {
        $key = env('ZOOM_CLIENT_ID');
        $secret = env('ZOOM_CLIENT_SECRET');
        $token_url = env('ZOOM_TOKEN_URL');
        $accountId = env('ZOOM_ACCOUNT_ID');


        $base64String = base64_encode(env('ZOOM_CLIENT_ID') . ':' . env('ZOOM_CLIENT_SECRET'));

        $response = Http::withHeaders([
            "Content-Type"=> "application/x-www-form-urlencoded",
            "Authorization"=> "Basic {$base64String}"
        ])->post("https://zoom.us/oauth/token?grant_type=account_credentials&account_id={$accountId}");

        $data = json_decode($response->getBody(), true);   
        return $data['access_token'];
    }

    protected function createAMeeting($data, $accessToken)
    {
        $request_url = 'https://api.zoom.us/v2/users/me/meetings';
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];
        $postFields = json_encode($data);   

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    /*
    *STRIPE SUCCESS METHOD TO REDIRECT AFTER PAYMENT SUCCESSFULL 
    */
    public function stripeSuccess($meeting_id) {
        try{
            //Fetch meeting data from the Db
            $meeting = Meeting::where('google_meet_id',$meeting_id)->first();
            if($meeting) {
                $authUser = Auth::user();
                $matchmaker = User::join('meetings', 'users.id' , 'meetings.matchmaker_id')->where('google_meet_id',$meeting_id)->first();
                
                // Notify the client
                $authUser->notify(new MeetingScheduledNotification($meeting));
                $matchmaker->notify(new MeetingScheduledNotification($meeting));
                
                return redirect()->to('/client/communication');
            } else {
                return response()->json('404 : No Meeting Found Or Invalid Meeting Id', JSON_PRETTY_PRINT);
            }
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    /**
     * Refresh the Google access token for a given user.
     *
     * @param User $user
     * @return array
     */
    public function refreshGoogleToken(User $user)
    {
        if (!$user || !$user->google_refresh_token) {
            return ['success' => false, 'message' => 'No refresh token available.'];
        }

        try {
            $client = new Google_Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setAccessType('offline');
            $client->refreshToken($user->google_refresh_token);
            $newToken = $client->getAccessToken();

            if (isset($newToken['access_token'])) {
                // Update tokens in the user model
                $user->google_access_token = $newToken['access_token'];
                if (isset($newToken['refresh_token'])) {
                    $user->google_refresh_token = $newToken['refresh_token'];
                }
                $user->google_token_expires_at = Carbon::now()->addSeconds($newToken['expires_in']);
                $user->save(); // Eloquent model's save method

                Log::info('Google tokens refreshed for user', ['user_id' => $user->id]);

                return ['success' => true];
            } else {
                Log::error('Failed to refresh Google token for user', ['user_id' => $user->id, 'response' => $newToken]);
                return ['success' => false, 'message' => 'Failed to refresh Google token.'];
            }
        } catch (\Exception $e) {
            Log::error('Exception during Google OAuth Token Refresh', ['exception' => $e->getMessage()]);
            return ['success' => false, 'message' => 'An error occurred while refreshing Google token.'];
        }
    }

    /**
     * Revoke the Google access token for a user.
     */
    public function revokeGoogleToken(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->google_access_token) {
            return response()->json(['error' => 'No access token available to revoke.'], 400);
        }

        try {
            $client = new Google_Client();
            $client->setAccessToken($user->google_access_token);

            $response = $client->revokeToken();

            if (!$response) {
                Log::error('Google OAuth Token Revocation Failed');
                return response()->json(['error' => 'Failed to revoke access token.'], 400);
            }

            // Clear tokens from the user model
            $user->google_access_token = null;
            $user->google_refresh_token = null;
            $user->google_token_expires_at = null;
            $user->save(); // Eloquent model's save method

            Log::info('Google access token revoked for user', ['user_id' => $user->id]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Exception during Google OAuth Token Revocation', ['exception' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while revoking Google token.'], 500);
        }
    }

    /**
     * Check if the authenticated user has authorized Google.
     */
    public function checkGoogleAuthorization()
    {
        $user = Auth::user();
        if ($user && $user->google_access_token) {
            if (Carbon::now()->gte($user->google_token_expires_at)) {
                $refreshResponse = $this->refreshGoogleToken($user);
                if (!$refreshResponse['success']) {
                    return response()->json(['authorized' => false, 'message' => 'Failed to refresh token.'], 500);
                }
            }
            return response()->json(['authorized' => true]);
        }
        return response()->json(['authorized' => false]);
    }

    // public function getMeeting(Request $request)
    // {
    //     // Assuming you have a Meeting model that interacts with your meetings table
    //     $meeting = Meeting::find($request->id);

    //     if (!$meeting) {
    //         return response()->json(['error' => 'Meeting not found'], 404);
    //     }

    //     // Generate the signature if necessary
    //     $signature = $this->generateSignature($meeting->google_meet_id); 

    //     return response()->json([
    //         'meetingId' => $meeting->google_meet_id,
    //         'password' => $meeting->google_meet_password,
    //         'signature' => $signature,
    //     ]);
    // }

    // private function generateSignature($meetingId)
    // {
    //     // Implement your signature generation logic here
    //     // You may need to pass the SDK key, secret, etc.
    //     return 'generated_signature'; // Replace with actual signature
    // }



    public function getMeeting(Request $request)
    {
        $apiKey = ''; // Your API Key
        $apiSecret = ''; // Your API Secret

        $meetingNumber = $request->input('meetingNumber'); // Get meeting number from request
        $role = $request->input('role'); // Get role from request

        // Current time
        $iat = time(); // Issued at
        $exp = $iat + 3600; // 1 hour expiration

        // Prepare payload
        $payload = [
            'appKey' => $apiKey,
            'mn' => '81977764813',
            'iat' => $iat,
            'exp' => $exp,
            'tokenExp' => $exp,
            'role' => 0
        ];

        // Generate JWT
        $token = JWT::encode($payload, $apiSecret, 'HS256'); // Specify algorithm here

        return response()->json(['signature' => $token]);
    }
    /**
     * Retrieve upcoming Google Meet meetings for the authenticated user.
     */
    // public function getUpcomingMeetings(Request $request)
    // {

    //     $user = Auth::user();

    //     if (!$user) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Unauthorized. Please log in.',
    //         ], 401);
    //     }

    //     // Fetch meetings where the user is a matchmaker
    //     $matchmakerMeetings = Meeting::with('clients')
    //         ->where('matchmaker_id', $user->id)
    //         ->where('start_time', '>=', Carbon::now())
    //         ->orderBy('start_time', 'asc')
    //         ->get();

    //     // Fetch meetings where the user is a client
    //     $clientMeetings = $user->meetingsAsClient()
    //         ->with('matchmaker')
    //         ->where('start_time', '>=', Carbon::now())
    //         ->orderBy('start_time', 'asc')
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => [
    //             'matchmakerMeetings' => $matchmakerMeetings,
    //             'clientMeetings' => $clientMeetings,
    //         ],
    //     ], 200);
    // }

    public function getUpcomingMeetings(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please log in.',
            ], 401);
        }

        if ($user->role === 'client') {
            // Fetch upcoming meetings along with matchmaker details for clients
            $clientMeetings = Meeting::with('matchmaker')
                ->where('client_id', $user->id)
                ->where('start_time', '>=', now())
                ->orderBy('start_time', 'asc')
                ->get()
                ->map(function ($meeting) {
                    return [
                        'id' => $meeting->id,
                        'client_id' => $meeting->client_id,
                        'matchmaker_id' => $meeting->matchmaker_id,
                        'google_meet_id' => $meeting->google_meet_id,
                        'google_meet_link' => $meeting->google_meet_link,
                        'start_time' => $meeting->start_time,
                        'duration' => $meeting->duration,
                        'status' => $meeting->status,
                        'amount' => $meeting->amount,
                        'matchmaker' => [
                            'name' => $meeting->matchmaker ? $meeting->matchmaker->name : null,
                            'email' => $meeting->matchmaker ? $meeting->matchmaker->email : null,
                        ],
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $clientMeetings,
            ], 200);
        } else {
            // Fetch upcoming meetings for matchmakers and group by google_meet_id
            $matchmakerMeetings = Meeting::where('matchmaker_id', $user->id)
                ->where('start_time', '>=', now())
                ->orderBy('start_time', 'asc')
                ->get()
                ->groupBy('google_meet_id') // Group by google_meet_id
                ->map(function ($meetings) {
                    // Take the first meeting from the grouped meetings
                    $firstMeeting = $meetings->first();
        
                    // Fetch client details for each meeting
                    $clients = User::whereIn('id', $meetings->pluck('client_id'))->get();
        
                    // Prepare the client information array
                    $clientDetails = $clients->map(function ($client) {
                        return [
                            'id' => $client->id,
                            'name' => $client->name,
                            'email' => $client->email,
                            // Add any other client details you need
                        ];
                    });
        
                    return [
                        'id' => $firstMeeting->id,
                        'matchmaker_id' => $firstMeeting->matchmaker_id,
                        'google_meet_id' => $firstMeeting->google_meet_id,
                        'google_meet_link' => $firstMeeting->google_meet_link,
                        'start_time' => $firstMeeting->start_time,
                        'duration' => $firstMeeting->duration,
                        'status' => $firstMeeting->status,
                        'amount' => $firstMeeting->amount,
                        'clients' => $clientDetails, // Include client information
                    ];
                })
                ->values() // Reset the keys to make it an indexed array
                ->all(); // Convert the collection to a plain array
        
            return response()->json([
                'success' => true,
                'data' => $matchmakerMeetings,
            ], 200);
        }        
    }
}
