<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\Auth\EmailVerificationCodeController;
use App\Http\Controllers\Auth\SocialiteController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');


// Google OAuth routes
Route::middleware('guest')->group(function () {
    Route::get('auth/google/redirect', [SocialiteController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('auth/google/callback', [SocialiteController::class, 'callback'])->name('auth.google.callback');
});

// Email verification code routes
Route::middleware('auth')->group(function () {
    Route::get('email/verify-code', [EmailVerificationCodeController::class, 'show'])->name('verification.code.show');
    Route::post('email/verify-code', [EmailVerificationCodeController::class, 'verify'])->name('verification.code.verify');
    Route::post('email/resend-code', [EmailVerificationCodeController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.code.resend');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__ . '/settings.php';
