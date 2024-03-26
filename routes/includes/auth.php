<?php

use Illuminate\Support\Facades\Route;
use SaguiAi\MixpostAdapter\Features;
use SaguiAi\MixpostAdapter\Http\Controllers\Auth\NewPasswordController;
use SaguiAi\MixpostAdapter\Http\Controllers\Auth\PasswordResetLinkController;
use SaguiAi\MixpostAdapter\Http\Controllers\Auth\TwoFactorAuthSessionController;
use SaguiAi\MixpostAdapter\Http\Middleware\Auth as MixpostAuthMiddleware;
use SaguiAi\MixpostAdapter\Http\Controllers\Auth\AuthenticatedController;
use SaguiAi\MixpostAdapter\Http\Controllers\Auth\InstallationController;
use SaguiAi\MixpostAdapter\Http\Middleware\RedirectIfAuthenticated;

Route::middleware(RedirectIfAuthenticated::class)->group(function () {
    Route::get('login', [AuthenticatedController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedController::class, 'store']);

    Route::get('installation', [InstallationController::class, 'create'])->name('installation');
    Route::post('installation', [InstallationController::class, 'store']);

    if (Features::isTwoFactorAuthEnabled()) {
        Route::get('two-factor-login', [TwoFactorAuthSessionController::class, 'create'])->name('two-factor.login');
        Route::post('two-factor-login', [TwoFactorAuthSessionController::class, 'store']);
    }

    if (Features::isForgotPasswordEnabled()) {
        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
    }
});

Route::middleware(MixpostAuthMiddleware::class)
    ->post('logout', [AuthenticatedController::class, 'destroy'])
    ->name('logout');
