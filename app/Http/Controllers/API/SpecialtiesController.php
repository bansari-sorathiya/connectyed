<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Specialties;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Response;

class SpecialtiesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }            

    /**
     * Update or create the specialties for the authenticated user.
     */
    public function updatespecialties(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'specialties.minage' => 'required|integer|min:18',
            'specialties.maxage' => 'required|integer|min:18|gte:specialties.minage',
            'specialties.gender' => 'required|in:male,female,male_female',
            'specialties.locations' => 'required|array|max:5',
            'specialties.locations.*' => 'string|max:255', // Optional: Validate each location
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Retrieve the authenticated user
        $user = Auth::user();       

        // Find or create the specialties for the user
        $specialties = $user->specialties ?? new Specialties(['user_id' => $user->id]);
        
        // Update specialties details
        $specialties->minage = $request->input('specialties.minage');
        $specialties->maxage = $request->input('specialties.maxage');
        $specialties->gender = $request->input('specialties.gender');
        
        // Convert array to JSON string for storage
        $specialties->locations = json_encode($request->input('specialties.locations'));
        
        // Save the specialties
        $specialties->save();        

        return response()->json([
            "success" => true,
            "data" => $specialties,
            "message" => 'Success'
        ], 200);
    }   

    /**
     * Retrieve the specialties for the authenticated user.
     */
    public function getspecialties()
    {
        $user = Auth::user();
        $specialties = Specialties::with('user')->where('user_id', $user->id)->first();
        
        if (!$specialties) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => 'Specialties not found'
            ], 404);
        }

        return response()->json([
            "success" => true,
            "data" => $specialties,
            "message" => 'Success'
        ], 200);
    }
    
    /**
     * Retrieve specialties by username.
     */
    public function getSpecialtiesByUsername($username)
    {
        // Assuming 'username' is a field in the users table
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => 'User not found'
            ], 404);
        }

        $specialties = Specialties::with('user')->where('user_id', $user->id)->first();
        
        if (!$specialties) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => 'Specialties not found for this user'
            ], 404);
        }

        return response()->json([
            "success" => true,
            "data" => $specialties,
            "message" => 'Success'
        ], 200);
    }  

}
