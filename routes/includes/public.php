<?php

use Illuminate\Support\Facades\Route;
use SaguiAi\MixpostAdapter\Util;
use SaguiAi\MixpostAdapter\Http\Controllers\Public\ManifestController;
use SaguiAi\MixpostAdapter\Http\Controllers\Public\PagesController;

Route::name('mixpost.')->group(function () {
    Route::get('manifest.json', ManifestController::class)->name('manifest');

    Route::prefix(Util::config('public_pages_prefix', ''))
        ->name('pages.')
        ->group(function () {
            Route::get('{slug?}', PagesController::class)->name('show');
        });
});
