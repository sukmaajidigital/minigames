<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\EmailVerificationCodeController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameSessionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MathGameController;
use App\Http\Controllers\PlayerProfileController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/games/math', [MathGameController::class, 'show'])->name('games.math');
Route::get('/games/leaderboard', [GameController::class, 'leaderboard'])->name('games.leaderboard');
Route::get('/players/{user}', [PlayerProfileController::class, 'show'])->name('players.show');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::redirect('/admin/login', '/login')->name('admin.login');

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

    Route::post('/games/sessions', [GameSessionController::class, 'store'])->name('game-sessions.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->middleware('admin')
        ->name('dashboard');
});

require __DIR__ . '/settings.php';
