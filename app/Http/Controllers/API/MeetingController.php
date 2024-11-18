<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Meeting; // Make sure to include the Meeting model

class MeetingController extends Controller
{
    /**
     * Show the meeting join page.
     *
     * @param  int  $meetingId
     * @return \Illuminate\View\View
     */
    public function joinMeeting($entryId)
    {
        // Fetch the meeting details based on the entry ID
        $meeting = Meeting::where('id', $entryId)->first();

        if (!$meeting) {
            return response()->json(['error' => 'Meeting not found'], 404);
        }

        $sdkKey = 'v0iNj3rgR4ub_Zm2JSva5w';
        $sdkSecret = 'mT8yR6kZX4x683hG92jpdiyp3cXCkxq1';
        
        // Generate the signature
        $signature = $this->generateSignature($sdkKey, $sdkSecret, $meeting->meeting_id, 0); // Role 0 for attendee

        return response()->json([
            'meetingId' => $meeting->google_meet_id,
            'password' => $meeting->google_meet_password,
            'signature' => $signature,
        ]);
    }

    private function generateSignature($sdkKey, $sdkSecret, $meetingNumber, $role)
    {
        $time = time(); // Current timestamp

        $payload = [
            "appKey" => $sdkKey,
            "mn" => $meetingNumber,
            "iat" => $time - 30,
            "exp" => $time + 1800,
            "tokenExp" => $time + 1800,
            "role" => $role,
        ];

        $header = json_encode(["alg" => "HS256", "typ" => "JWT"]);
        $payload = json_encode($payload);

        $base64Header = $this->base64UrlEncode($header);
        $base64Payload = $this->base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, $sdkSecret, true);
        $base64Signature = $this->base64UrlEncode($signature);

        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }

    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
