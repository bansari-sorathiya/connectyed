<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\WithdrawalRequest;

class MatchmakerController extends Controller
{
    /**
     * Display a listing of approved matchmakers.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $matchmakers = User::with(['profile', 'specialties', 'availability'])
                ->where('role', 'matchmaker')
                ->get();

            Log::info('Fetched approved matchmakers');

            return response()->json([
                'success' => true,
                'data' => $matchmakers,
                'message' => 'Successfully retrieved matchmakers.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching matchmakers', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching matchmakers.'
            ], 500);
        }
    }

    /**
     * Retrieve the total earnings of the authenticated matchmaker.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEarnings(Request $request)
    {
        try {
            $matchmaker = Auth::user();

            // Ensure the authenticated user is a matchmaker
            if ($matchmaker->role !== 'matchmaker') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }

            $totalEarnings = Meeting::where('matchmaker_id', $matchmaker->id)
                ->where('status', 'confirmed')
                ->sum('matchmaker_earnings');

            Log::info("Fetched earnings for matchmaker ID: {$matchmaker->id}");

            return response()->json([
                'success' => true,
                'totalEarnings' => $totalEarnings,
                'message' => 'Earnings retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching earnings', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching earnings.'
            ], 500);
        }
    }

    /**
     * Handle withdrawal requests from the authenticated matchmaker.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestWithdrawal(Request $request)
    {
        try {
            $matchmaker = Auth::user();

            // Ensure the authenticated user is a matchmaker
            if ($matchmaker->role !== 'matchmaker') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ], 403);
            }

            $totalEarnings = Meeting::where('matchmaker_id', $matchmaker->id)
                ->where('status', 'confirmed')
                ->sum('matchmaker_earnings');

            // Validate if there are earnings to withdraw
            if ($totalEarnings <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No earnings available for withdrawal.'
                ], 400);
            }

            // Send email to admin with withdrawal request details
            Mail::to('admin@connectyed.com')
                ->cc('george@connectyed.com')
                ->send(new WithdrawalRequest($matchmaker, $totalEarnings));

            Log::info("Withdrawal request sent for matchmaker ID: {$matchmaker->id}");

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request sent successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error processing withdrawal request', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your withdrawal request.'
            ], 500);
        }
    }
}
