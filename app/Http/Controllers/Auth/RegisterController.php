<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use App\Models\Specialties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed|max:100',
            'privacypolicy' => 'required|boolean',
            'termsofuse' => 'required|boolean',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'currentLocation' => 'nullable|string|max:255',
            'age' => 'required|integer|min:1|max:120',
            'gender' => 'required|string|max:50',
            'hairColor' => 'required|string|max:50',
            'bodyType' => 'required|string|max:50',
            'heightFeet' => 'required|integer|min:1|max:8',
            'heightInches' => 'required|integer|min:0|max:11',
            'maritalStatus' => 'required|string|max:50',
            'children' => 'required|integer|min:0|max:4',
            'religion' => 'required|string|max:100',
            'smoker' => 'required|boolean',
            'drinker' => 'required|string|in:None,Occasionally,Often',
            'education' => 'required|string|max:255',
            'jobTitle' => 'required|string|max:255',
            'sports' => 'required|string',
            'hobbies' => 'required|string',
            'englishLevel' => 'required|string|max:50',
            'languages' => 'required|string',
            'ismatchmaker' => 'nullable|boolean',
            // Google fields if applicable
            'google_access_token' => 'nullable|string',
            'google_refresh_token' => 'nullable|string',
            'google_token_expires_at' => 'nullable|date',
        ]);

        // Handle validation failure
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Extract validated data
        $data = $validator->validated();

        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => isset($data['ismatchmaker']) && $data['ismatchmaker'] ? 'candidate' : 'client',
            'google_access_token' => $data['google_access_token'] ?? null,
            'google_refresh_token' => $data['google_refresh_token'] ?? null,
            'google_token_expires_at' => $data['google_token_expires_at'] ?? null,
        ]);

        // Create the profile
        $profile = Profile::create([
            'user_id' => $user->id,
            'city' => $data['city'],
            'state' => $data['state'],
            'country' => $data['country'],
            'location' => $data['currentLocation'] ?? null,
            'age' => $data['age'],
            'gender' => $data['gender'],
            'haircolor' => $data['hairColor'],
            'bodytype' => $data['bodyType'],
            'height' => $data['heightFeet'],
            'inches' => $data['heightInches'],
            'maritalstatus' => $data['maritalStatus'],
            'children' => $data['children'],
            'religion' => $data['religion'],
            'smoker' => $data['smoker'],
            'drinker' => $data['drinker'],
            'education' => $data['education'],
            'jobtitle' => $data['jobTitle'],
            'sports' => $data['sports'],
            'hobbies' => $data['hobbies'],
            'english' => $data['englishLevel'],
            'languages' => $data['languages'],
        ]);

        // If the user is a matchmaker, create a specialties record
        if (isset($data['ismatchmaker']) && $data['ismatchmaker']) {
            Specialties::create([
                'user_id' => $user->id,
                // Add additional specialties fields if necessary
            ]);
        }

        // Trigger the Registered event to send verification email
        event(new Registered($user));

        // Generate JWT token for the new user (optional)
        // $token = JWTAuth::fromUser($user);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'User registered successfully. Please check your email for verification.',
            // 'authorization' => [
            //     'token' => $token,
            //     'type' => 'bearer',
            // ],
            'data' => [
                'user' => $user,
            ]
        ], 201);
    }
}
