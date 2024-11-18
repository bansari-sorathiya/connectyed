<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application using JWT
    | and redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     * 
     * Note: This property is not used in API-based authentication.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * Applies middleware to restrict access to certain methods.
     *
     * @return void
     */
    public function __construct()
    {
        // Apply 'guest' middleware except for the 'logout' method
        $this->middleware('guest')->except('logout');
    }

    /**
     * Override the default login method to implement JWT authentication.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Extract username and password from the request
        $credentials = $request->only('username', 'password');

        // Define validation rules
        $validator = Validator::make($credentials, [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // If validation fails, return errors with 422 status
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Attempt to authenticate the user and generate a JWT token
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid username or password.',
            ], 401);
        }

        // Retrieve the authenticated user
        $user = Auth::user();

        // Check if the user's email is verified
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Please verify your email before logging in.',
            ], 403);
        }

        // Prepare the response data
        return response()->json([
            'success' => true,
            'message' => 'Authorized',
            'data' => [
                'user' => $user,
            ],
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ], 200);
    }

    /**
     * Override the default logout method to invalidate the JWT token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Invalidate the token
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout, please try again.',
            ], 500);
        }
    }

    /**
     * Refresh a JWT token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh(JWTAuth::getToken());
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'user' => $user,
                ],
                'authorization' => [
                    'token' => $newToken,
                    'type' => 'bearer',
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed',
            ], 401);
        }
    }

    /**
     * Introspect the token and return user info.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function introspect()
    {
        try {
            $user = Auth::user();
            if ($user) {
                return response()->json([
                    'success' => true,
                    'message' => 'Authorized',
                    'data' => [
                        'user' => $user,
                        'profile' => $user->profile,
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }
    }

    /**
     * Resend verification email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => 'Email already verified.',
            ], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification link sent!',
        ], 200);
    }
}
