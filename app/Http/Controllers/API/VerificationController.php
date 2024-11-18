<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        // Remove any query parameters from the signature verification
        $originalRequest = Request::create($request->fullUrl());
        
        if (! URL::hasValidSignature($originalRequest)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid verification link or link has expired.'
            ], 403);
        }

        $user = User::findOrFail($id);
        
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Email already verified.'
            ], 200);
        }

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid verification link.'
            ], 403);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Email successfully verified.'
        ], 200);
    }
}