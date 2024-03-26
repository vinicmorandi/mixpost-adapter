<?php

use Illuminate\Support\Facades\Route;
use SaguiAi\MixpostAdapter\Http\Middleware\Auth as MixpostAuthMiddleware;
use SaguiAi\MixpostAdapter\Http\Middleware\HandleInertiaRequests;
use SaguiAi\MixpostAdapter\Http\Middleware\Localization;
use SaguiAi\MixpostAdapter\Mixpost;
use SaguiAi\MixpostAdapter\Util;

Route::prefix(Util::corePath())
    ->name('mixpost.')
    ->middleware(array_merge(['web', Localization::class], Mixpost::getGlobalMiddlewares()))
    ->group(function () {
        // Auth routes
        Route::middleware(HandleInertiaRequests::class)->group(function () {
            require __DIR__ . '/includes/auth.php';
        });

        // App routes
        Route::middleware([
            MixpostAuthMiddleware::class,
            HandleInertiaRequests::class
        ])->group(function () {
            require __DIR__ . '/includes/main.php';

            require __DIR__ . '/includes/admin.php';

            require __DIR__ . '/includes/workspace.php';
        });
    });

require __DIR__ . '/includes/callback.php';

require __DIR__ . '/includes/public.php';
