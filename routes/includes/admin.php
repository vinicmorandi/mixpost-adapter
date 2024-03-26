<?php

use Illuminate\Support\Facades\Route;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\BlocksController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\Configs\ThemeConfigController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\DashboardController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\DeletePagesController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\DeleteUsersController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\DeleteWorkspacesController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\GeneratePageSamplesController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\PagesController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\SystemLogsController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\SystemStatusController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\UploadFileController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\UserItemsController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\WorkspaceItemsController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\WorkspacesController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\UsersController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\ServicesController;
use SaguiAi\MixpostAdapter\Http\Controllers\Admin\WorkspaceUsersController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\CreateMastodonAppController;
use SaguiAi\MixpostAdapter\Http\Middleware\Admin;
use SaguiAi\MixpostAdapter\Http\Middleware\EnterpriseConsoleRedirects;
use SaguiAi\MixpostAdapter\Http\Middleware\HandleInertiaRequests;

Route::prefix('admin')->middleware([Admin::class])->group(function () {
    Route::get('/', DashboardController::class)->name('dashboardAdmin');

    Route::prefix('workspaces')->name('workspaces.')->middleware(EnterpriseConsoleRedirects::class)->group(function () {
        Route::get('/', [WorkspacesController::class, 'index'])->name('index');
        Route::get('/', [WorkspacesController::class, 'index'])->name('index');
        Route::get('create', [WorkspacesController::class, 'create'])->name('create');
        Route::post('/', [WorkspacesController::class, 'store'])->name('store');
        Route::get('{workspace}', [WorkspacesController::class, 'view'])->name('view');
        Route::get('{workspace}/edit', [WorkspacesController::class, 'edit'])->name('edit');
        Route::put('{workspace}', [WorkspacesController::class, 'update'])->name('update');
        Route::delete('{workspace}', [WorkspacesController::class, 'delete'])->name('delete');
        Route::delete('/', DeleteWorkspacesController::class)->name('multipleDelete');

        Route::prefix('resources')->name('resources.')->group(function () {
            Route::get('items', WorkspaceItemsController::class)->name('items');
        });

        Route::prefix('{workspace}/users')->name('users.')->group(function () {
            Route::post('/', [WorkspaceUsersController::class, 'store'])->name('store');
            Route::put('/', [WorkspaceUsersController::class, 'update'])->name('update');
            Route::delete('/', [WorkspaceUsersController::class, 'destroy'])->name('delete');
        });
    });

    Route::prefix('users')->name('users.')->middleware(EnterpriseConsoleRedirects::class)->group(function () {
        Route::get('/', [UsersController::class, 'index'])->name('index');
        Route::get('create', [UsersController::class, 'create'])->name('create');
        Route::post('/', [UsersController::class, 'store'])->name('store');
        Route::get('{user}', [UsersController::class, 'view'])->name('view');
        Route::get('{user}/edit', [UsersController::class, 'edit'])->name('edit');
        Route::put('{user}/update', [UsersController::class, 'update'])->name('update');
        Route::delete('{user}', [UsersController::class, 'delete'])->name('delete');
        Route::delete('/', DeleteUsersController::class)->name('multipleDelete');

        Route::prefix('resources')->name('resources.')->group(function () {
            Route::get('items', UserItemsController::class)->name('items');
        });
    });

    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/', [ServicesController::class, 'index'])->name('index');
        Route::put('{service}', [ServicesController::class, 'update'])->name('update');

        Route::post('create-mastodon-app', CreateMastodonAppController::class)
            ->withoutMiddleware([HandleInertiaRequests::class, Admin::class])
            ->name('createMastodonApp');
    });

    Route::prefix('pages')->name('pages.')->group(function () {
        Route::get('/', [PagesController::class, 'index'])->name('index');
        Route::get('create', [PagesController::class, 'create'])->name('create');
        Route::post('/', [PagesController::class, 'store'])->name('store');
        Route::get('{page}', [PagesController::class, 'edit'])->name('edit');
        Route::put('{page}', [PagesController::class, 'update'])->name('update');
        Route::delete('{page}', [PagesController::class, 'delete'])->name('delete');
        Route::delete('/', DeletePagesController::class)->name('multipleDelete');

        Route::post('generate-samples', GeneratePageSamplesController::class)->name('generateSamples');
    });

    Route::prefix('blocks')->name('blocks.')->group(function () {
        Route::post('/', [BlocksController::class, 'store'])->name('store');
        Route::put('{block}', [BlocksController::class, 'update'])->name('update');
        Route::delete('{block}', [BlocksController::class, 'delete'])->name('delete');
    });

    Route::prefix('configs')->name('configs.')->group(function () {
        Route::prefix('theme')->name('theme.')->group(function () {
            Route::get('/', [ThemeConfigController::class, 'form'])->name('form');
            Route::put('/', [ThemeConfigController::class, 'update'])->name('update');
        });
    });

    Route::prefix('system')->name('system.')->group(function () {
        Route::get('status', SystemStatusController::class)->name('status');

        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [SystemLogsController::class, 'index'])->name('index');
            Route::get('download', [SystemLogsController::class, 'download'])->name('download');
            Route::delete('clear', [SystemLogsController::class, 'clear'])->name('clear');
        });
    });

    Route::prefix('files')->name('files.')->group(function () {
        Route::post('upload', UploadFileController::class)->name('upload');
    });
});
