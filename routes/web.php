<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\GoogleMeetController;

// Ensure login route is defined
Route::get('/login', function () {
    return view('welcome'); // Adjust if your Vue app uses a different entry point
})->name('login');

// Google OAuth Routes
Route::middleware(['jwt.cookie', 'auth:api'])->group(function () {
    Route::get('/google/redirect', [GoogleMeetController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/google/callback', [GoogleMeetController::class, 'handleGoogleCallback'])->name('google.callback');
});


Route::middleware([\App\Http\Middleware\JwtCookieMiddleware::class, 'auth:api'])->group(function () {
    Route::get('/google/redirect', [GoogleMeetController::class, 'redirectToGoogle'])->name('google.redirect');
    Route::get('/google/callback', [GoogleMeetController::class, 'handleGoogleCallback'])->name('google.callback');
});



Route::get('{any}', function () {
     return view('welcome'); // Ensure this serves your Vue app's entry point
})->where('any', '.*');

