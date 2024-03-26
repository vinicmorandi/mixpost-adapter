<?php

use Illuminate\Support\Facades\Route;
use SaguiAi\MixpostAdapter\Util;
use SaguiAi\MixpostAdapter\Http\Middleware\Auth as MixpostAuthMiddleware;
use SaguiAi\MixpostAdapter\Http\Middleware\HandleInertiaRequests;
use SaguiAi\MixpostAdapter\Http\Middleware\CheckWorkspaceUser;
use SaguiAi\MixpostAdapter\Http\Middleware\IdentifyWorkspaceForCallback;
use SaguiAi\MixpostAdapter\Http\Controllers\Main\CallbackSocialProviderController;

$prefix = Util::config('force_core_path_callback_to_native', false) ?
    'mixpost' :
    Util::corePath();

Route::middleware([
    'web',
    MixpostAuthMiddleware::class,
    HandleInertiaRequests::class,
    IdentifyWorkspaceForCallback::class,
    CheckWorkspaceUser::class
])->prefix($prefix)
    ->get('callback/{provider}', CallbackSocialProviderController::class)
    ->name('mixpost.callbackSocialProvider');
