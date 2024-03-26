<?php

namespace SaguiAi\MixpostAdapter;

use Illuminate\Database\Eloquent\Builder;
use SaguiAi\MixpostAdapter\Jobs\WorkspaceArtisanJob;
use SaguiAi\MixpostAdapter\Models\Workspace;

class Schedule
{
    public static function register($schedule, ?Builder $query = null): void
    {
        $query = $query ?? Workspace::query()->select(['id', 'name']);

        $query
            ->each(function (Workspace $workspace) use ($schedule): void {
                if (!$workspace->valid()) {
                    return;
                }

                $schedule
                    ->job(new WorkspaceArtisanJob($workspace, 'mixpost:run-scheduled-posts'))
                    ->name("$workspace->name - mixpost:run-scheduled-posts")
                    ->everyMinute();

                $schedule
                    ->job(new WorkspaceArtisanJob($workspace, 'mixpost:import-account-data'))
                    ->name("$workspace->name - mixpost:import-account-data")
                    ->everyTwoHours();

                $schedule
                    ->job(new WorkspaceArtisanJob($workspace, 'mixpost:import-account-audience'))
                    ->name("$workspace->name - mixpost:import-account-audience")
                    ->everyThreeHours();

                $schedule
                    ->job(new WorkspaceArtisanJob($workspace, 'mixpost:process-metrics'))
                    ->name("$workspace->name - mixpost:process-metrics")
                    ->everyThreeHours();

                $schedule
                    ->job(new WorkspaceArtisanJob($workspace, 'mixpost:check-and-refresh-account-token'))
                    ->name("$workspace->name - mixpost:check-and-refresh-account-token")
                    ->everyFifteenMinutes();

                $schedule
                    ->job(new WorkspaceArtisanJob($workspace, 'mixpost:prune-trashed-posts'))
                    ->name("$workspace->name - mixpost:prune-trashed-posts")
                    ->daily();
            });
    }
}
