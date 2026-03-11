<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ── Registration ──────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);
});

// ── Login (form lives inside register.blade.php via tabs) ─────────────────────
Route::middleware('guest')->group(function () {
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('login');
});

// ── Logout ────────────────────────────────────────────────────────────────────
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ── Password Reset ────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// ── Email Verification ────────────────────────────────────────────────────────
// Required for student registration — sendEmailVerificationNotification() uses
// the verification.verify route to build the signed URL sent in the email.

Route::middleware('auth')->group(function () {

    // Step 1 — "Check your email" holding page shown after registration / login
    Route::get('email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // Step 2 — Signed link clicked inside the verification email
    Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    })->middleware(['signed', 'throttle:6,1'])
      ->name('verification.verify');

    // Step 3 — Resend button on the verify-email page
    Route::post('email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')
      ->name('verification.send');

});