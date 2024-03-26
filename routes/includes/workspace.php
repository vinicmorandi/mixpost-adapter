<?php

use Illuminate\Support\Facades\Route;
use SaguiAi\MixpostAdapter\Enums\WorkspaceUserRole;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\AccountEntitiesController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\AccountsController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\AddAccountController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\AddPostToQueueController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\CalendarController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\DashboardController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\DeletePostsController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\DuplicatePostController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\PostingScheduleController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\RestorePostController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\MediaController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\MediaDownloadExternalController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\MediaFetchGifsController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\MediaFetchStockController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\MediaFetchUploadsController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\MediaUploadFileController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\PostsController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\ReportsController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\SchedulePostController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\SocialProvider\Pinterest\StorePinterestBorderController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\SwitchWorkspaceController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\TagsController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\HashtagGroupsController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\TemplatesController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\UpdateAccountSuffixController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\VariablesController;
use SaguiAi\MixpostAdapter\Http\Controllers\Workspace\TemplatesApiController;
use SaguiAi\MixpostAdapter\Http\Middleware\CheckWorkspaceUser;
use SaguiAi\MixpostAdapter\Http\Middleware\IdentifyWorkspace;
use SaguiAi\MixpostAdapter\Http\Middleware\HandleInertiaRequests;
use SaguiAi\MixpostAdapter\Mixpost;

Route::middleware(array_merge([
    IdentifyWorkspace::class,
    CheckWorkspaceUser::class
], Mixpost::getWorkspaceMiddlewares()))
    ->prefix('{workspace}')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::post('switch', SwitchWorkspaceController::class)->name('switchWorkspace');
        Route::get('reports', ReportsController::class)->name('reports');

        Route::middleware(CheckWorkspaceUser::class . ':' . WorkspaceUserRole::ADMIN->name)
            ->group(function () {
                Route::prefix('accounts')->name('accounts.')->group(function () {
                    Route::get('/', [AccountsController::class, 'index'])->name('index');
                    Route::post('add/{provider}', AddAccountController::class)->name('add');
                    Route::put('update/{account}', [AccountsController::class, 'update'])->name('update');
                    Route::delete('{account}', [AccountsController::class, 'delete'])->name('delete');

                    Route::put('update-suffix/{account}', UpdateAccountSuffixController::class)->name('updateSuffix');

                    Route::prefix('entities')->name('entities.')->group(function () {
                        Route::get('{provider}', [AccountEntitiesController::class, 'index'])->name('index');
                        Route::post('{provider}', [AccountEntitiesController::class, 'store'])->name('store');
                    });
                });

                Route::prefix('posting-schedule')->name('postingSchedule.')->group(function () {
                    Route::get('/', [PostingScheduleController::class, 'index'])->name('index');
                    Route::put('/', [PostingScheduleController::class, 'update'])->name('update');
                });
            });

        Route::prefix('posts')->name('posts.')->group(function () {
            Route::get('/', [PostsController::class, 'index'])->name('index');
            Route::get('create/{schedule_at?}', [PostsController::class, 'create'])
                ->name('create')
                ->where('schedule_at', '^(\d{4})-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]) (0\d|1\d|2[0-3]):([0-5]\d)$');
            Route::post('store', [PostsController::class, 'store'])->name('store');
            Route::get('{post}', [PostsController::class, 'edit'])->name('edit');
            Route::put('{post}', [PostsController::class, 'update'])->name('update');
            Route::delete('{post}', [PostsController::class, 'destroy'])->name('delete');

            Route::post('schedule/{post}', SchedulePostController::class)->name('schedule');
            Route::post('add-to-queue/{post}', AddPostToQueueController::class)->name('addToQueue');
            Route::post('duplicate/{post}', DuplicatePostController::class)->name('duplicate');
            Route::post('restore/{post}', RestorePostController::class)->name('restore');
            Route::delete('/', DeletePostsController::class)->name('multipleDelete');
        });

        Route::get('calendar/{date?}/{type?}', [CalendarController::class, 'index'])
            ->name('calendar')
            ->where('date', '^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$')
            ->where('type', '^(?:month|week)$');

        Route::prefix('media')->name('media.')->group(function () {
            Route::get('/', [MediaController::class, 'index'])->name('index');
            Route::delete('/', [MediaController::class, 'destroy'])->name('delete');
            Route::get('fetch/uploaded', MediaFetchUploadsController::class)->name('fetchUploads');
            Route::get('fetch/stock', MediaFetchStockController::class)->name('fetchStock');
            Route::get('fetch/gifs', MediaFetchGifsController::class)->name('fetchGifs');
            Route::post('download', MediaDownloadExternalController::class)->name('download');
            Route::post('upload', MediaUploadFileController::class)->name('upload');
        })->withoutMiddleware(HandleInertiaRequests::class);

        Route::prefix('tags')->name('tags.')->group(function () {
            Route::post('/', [TagsController::class, 'store'])->name('store');
            Route::put('{tag}', [TagsController::class, 'update'])->name('update');
            Route::delete('{tag}', [TagsController::class, 'destroy'])->name('delete');
        });

        Route::prefix('hashtaggroups')->name('hashtaggroups.')->group(function () {
            Route::get('/', [HashtagGroupsController::class, 'index'])->name('index');
            Route::post('/', [HashtagGroupsController::class, 'store'])->name('store');
            Route::put('{hashtaggroup}', [HashtagGroupsController::class, 'update'])->name('update');
            Route::delete('{hashtaggroup}', [HashtagGroupsController::class, 'destroy'])->name('delete');
        });

        Route::prefix('variables')->name('variables.')->group(function () {
            Route::get('/', [VariablesController::class, 'index'])->name('index');
            Route::post('/', [VariablesController::class, 'store'])->name('store');
            Route::put('{variable}', [VariablesController::class, 'update'])->name('update');
            Route::delete('{variable}', [VariablesController::class, 'destroy'])->name('delete');
        });

        Route::prefix('templates')->name('templates.')->group(function () {
            Route::get('/', [TemplatesController::class, 'index'])->name('index');
            Route::get('create', [TemplatesController::class, 'create'])->name('create');
            Route::get('edit/{template}', [TemplatesController::class, 'edit'])->name('edit');

            Route::prefix('api')->name('api.')->group(function () {
                Route::get('/', [TemplatesApiController::class, 'index'])->name('index');
                Route::post('/', [TemplatesApiController::class, 'store'])->name('store');
                Route::put('{template}', [TemplatesApiController::class, 'update'])->name('update');
                Route::delete('{template}', [TemplatesApiController::class, 'destroy'])->name('delete');
            });
        });

        Route::prefix('provider')->name('provider.')->group(function () {
            Route::prefix('pinterest')->name('pinterest.')->group(function () {
                Route::post('store-board', StorePinterestBorderController::class)->name('storeBoard');
            });
        });
    });
