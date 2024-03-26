<?php

use Illuminate\Support\Facades\Route;
use SaguiAi\MixpostAdapter\Features;
use SaguiAi\MixpostAdapter\Http\Controllers\Main\ConfirmPasswordController;
use SaguiAi\MixpostAdapter\Http\Controllers\Main\TwoFactorAuthController;
use SaguiAi\MixpostAdapter\Http\Controllers\Main\UpdateAuthUserPasswordController;
use SaguiAi\MixpostAdapter\Http\Controllers\Main\HomeController;
use SaguiAi\MixpostAdapter\Http\Controllers\Main\ProfileController;
use SaguiAi\MixpostAdapter\Http\Controllers\Main\UpdateAuthUserController;
use SaguiAi\MixpostAdapter\Http\Controllers\Main\UpdateAuthUserPreferencesController;
use SaguiAi\MixpostAdapter\Http\Middleware\EnsurePasswordIsConfirmed;

Route::get('/', HomeController::class)->name('home');

Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('preferences', UpdateAuthUserPreferencesController::class)->name('updatePreferences');
    Route::put('user', UpdateAuthUserController::class)->name('updateUser');
    Route::put('password', UpdateAuthUserPasswordController::class)->name('updatePassword');
    Route::post('confirm-password', ConfirmPasswordController::class)->name('confirmPassword');

    if (Features::isTwoFactorAuthEnabled()) {
        Route::prefix('two-factor-auth')
            ->name('two-factor-auth.')
            ->middleware(EnsurePasswordIsConfirmed::class)
            ->group(function () {
                Route::post('enable', [TwoFactorAuthController::class, 'enable'])->name('enable');
                Route::post('confirm', [TwoFactorAuthController::class, 'confirm'])->name('confirm');
                Route::get('recovery-codes', [TwoFactorAuthController::class, 'showRecoveryCodes'])->name('showRecoveryCodes');
                Route::post('regenerate-recovery-codes', [TwoFactorAuthController::class, 'regenerateRecoveryCodes'])->name('regenerateRecoveryCodes');
                Route::delete('disable', [TwoFactorAuthController::class, 'disable'])->name('disable');
            });
    }
});

