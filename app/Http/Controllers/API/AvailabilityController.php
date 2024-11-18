<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Availability;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AvailabilityResource;
use Illuminate\Support\Facades\Validator;
class AvailabilityController extends Controller
{
    /**
     * Display a listing of the availabilities.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function index($user_id)
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json([
                "success" => false,
                "message" => 'Unauthenticated.'
            ], 401);
        }

        // Since all roles can view all availabilities, no role check is needed
        // Fetch availabilities
        $availabilities = Availability::where('user_id', $user_id)->get();

        return response()->json([
            "success" => true,
            "data" => AvailabilityResource::collection($availabilities),
            "message" => 'Availabilities retrieved successfully.'
        ], 200);
    }

    /**
     * Store a newly created availability in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json([
                "success" => false,
                "message" => 'Unauthenticated.'
            ], 401);
        }

        // Validation Rules
        $rules = [
            'availabilities' => 'required|array|min:1',
            'availabilities.*.start_date' => 'required|date|after_or_equal:today',
            'availabilities.*.end_date' => 'required|date|after_or_equal:availabilities.*.start_date',
            'availabilities.*.is_all_day' => 'boolean',
            'availabilities.*.start_time' => 'nullable|date_format:H:i',
            'availabilities.*.end_time' => 'nullable|date_format:H:i|after:availabilities.*.start_time',
        ];

        // Validate the request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "errors" => $validator->errors(),
                "message" => 'Validation failed.'
            ], 422);
        }

        // Store availabilities
        foreach ($request->availabilities as $availabilityData) {
            Availability::create([
                'user_id' => $currentUser->id, // Assign to the authenticated user
                'start_date' => $availabilityData['start_date'],
                'end_date' => $availabilityData['end_date'],
                'is_all_day' => $availabilityData['is_all_day'],
                'start_time' => $availabilityData['is_all_day'] ? null : $availabilityData['start_time'],
                'end_time' => $availabilityData['is_all_day'] ? null : $availabilityData['end_time'],
            ]);
        }

        return response()->json([
            "success" => true,
            "data" => [],
            "message" => 'Availability created successfully.'
        ], 201);
    }

    /**
     * Remove the specified availability from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $currentUser = Auth::user();

        if (!$currentUser) {
            return response()->json([
                "success" => false,
                "message" => 'Unauthenticated.'
            ], 401);
        }

        $availability = Availability::find($id);

        if (!$availability) {
            return response()->json([
                "success" => false,
                "message" => 'Availability not found.'
            ], 404);
        }

        // Since all roles can delete any availability, no role check is needed
        $availability->delete();

        return response()->json([
            "success" => true,
            "data" => [],
            "message" => 'Availability deleted successfully.'
        ], 200);
    }
}
