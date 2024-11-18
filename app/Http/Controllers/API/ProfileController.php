<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['getdetail']]);
    }            

    /**
     * Update the specified resource in storage.
     */
    public function updateprofile(Request $request)
    {
        $user = Auth::user();
        
        // Validate the request
        $request->validate([
            'profile.name' => 'required|string|max:255',
            'profile.city' => 'nullable|string|max:255',
            'profile.state' => 'nullable|string|max:255',
            'profile.country' => 'nullable|string|max:255',
            'profile.yearsexperience' => 'nullable|numeric',
            'profile.bio' => 'nullable|string',
            'profile.jobtitle' => 'nullable|string|max:255',
        ]);

        // Update user details - only if name is provided
        if ($request->has('profile.name')) {
            $user->name = $request->input('profile.name');
            $user->save();
        }

        // Update profile details
        $profile = Profile::where('user_id', $user->id)->first();
        
        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
        }

        // Update only the fields that are present in the request
        $fields = [
            'name', 'city', 'state', 'country', 'yearsexperience',
            'jobtitle', 'bio', 'profile_image1', 'profile_image2'
        ];

        foreach ($fields as $field) {
            if ($request->has("profile.$field")) {
                $profile->$field = $request->input("profile.$field");
            }
        }

        $profile->save();

        return response()->json([
            "success" => true,
            "data" => $profile,
            "message" => 'Profile updated successfully'
        ], 200);
    }

    /**
     * Upload profile image
     */
    public function uploadimage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_number' => 'required|in:1,2'
        ]);

        $user = Auth::user();
        
        // Generate a unique filename
        $fileName = Str::uuid() . '.' . $request->file->getClientOriginalExtension();
        
        // Store the file
        $filePath = $request->file('file')->storeAs(
            'upload/images/profiles/'.$user->id, 
            $fileName, 
            'public'
        );

        // Update the profile with the new image path
        $profile = Profile::where('user_id', $user->id)->first();
        
        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
        }

        $imageField = 'profile_image' . $request->input('image_number');
        $profile->$imageField = '/storage/' . $filePath;
        $profile->save();

        return response()->json([
            "success" => true,
            "data" => '/storage/' . $filePath,
            "message" => 'Image uploaded successfully'
        ], 200);
    }

    /**
     * Get profile details
     */
    public function getprofile()
    {
        $user = Auth::user();
        $profile = Profile::with('user')->where('user_id', $user->id)->first();

        if (!$profile) {
            $profile = new Profile();
            $profile->user_id = $user->id;
            $profile->name = $user->name;
            $profile->save();
            $profile->load('user');
        }
        
        return response()->json([
            "success" => true,
            "data" => $profile,
            "message" => 'Success'
        ], 200);     
    }

    public function getdetail($username)
    {
        $profile = Profile::with(['user.availability' => function ($query) {
            $query->orderBy('start_date', 'asc')->limit(2);
        }])
        ->whereHas('user', function ($query) use ($username) {
            $query->where('username', $username);
        })
        ->firstOrFail();
        
        $profileList = Profile::with('user')
            ->where('matchmaker_id', $profile->user_id)
            ->limit(4)
            ->get();

        $specialties = Specialties::with('user')
            ->where('user_id', $profile->user_id)
            ->first();

        return response()->json([
            "success" => true,
            "data" => [
                'profile' => $profile,      
                'profile_list' => $profileList,
                'specialties' => $specialties
            ],
            "message" => 'Success'
        ], 200);
    }
}