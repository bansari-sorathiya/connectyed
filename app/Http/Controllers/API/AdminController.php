<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Profile;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }   

    /**
     * Fetch users with role 'candidate' along with their profiles.
     */
    public function getCandidates()
    {
        // Eager load the 'profile' relationship
        $candidates = User::where('role', 'candidate')
                          ->with('profile') // Include profile data
                          ->get();        
                          
        return response()->json([
            "success" => true,
            "data" => $candidates,
            "message" => 'Success'
        ], 200);
    }

    /**
     * Approve a candidate by changing their role to 'matchmaker'.
     */
    public function approveCandidate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);
        $user->role = 'matchmaker';
        $user->save();
        
        return response()->json([
            "success" => true,
            "data" => $user,
            "message" => 'Candidate approved successfully!'
        ], 200);
    }

    /**
     * Fetch users with role 'client' along with their profiles.
     */
    public function getClients()
    {
        $clients = User::with('profile')
                       ->where('role', 'client')
                       ->get();        

        return response()->json([
            "success" => true,
            "data" => $clients,
            "message" => 'Success'
        ], 200);
    }
}
