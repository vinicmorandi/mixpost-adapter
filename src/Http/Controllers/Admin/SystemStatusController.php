<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Admin;

use Composer\InstalledVersions;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Mixpost;
use SaguiAi\MixpostAdapter\Support\HorizonStatus;

class SystemStatusController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('Admin/System/Status', [
            'env' => App::environment(),
            'debug' => config('app.debug'),
            'horizon_status' => resolve(HorizonStatus::class)->get(),
            'has_queue_connection' => config('queue.connections.mixpost-redis') && !empty(config('queue.connections.mixpost-redis')),
            'last_scheduled_run' => $this->getLastScheduleRun(),
//            'scheduled_tasks' => $this->getScheduledTasks(),
            'base_path' => base_path(),
            'disk' => config('mixpost.disk'),
            'log_channel' => config('mixpost.log_channel') ? config('mixpost.log_channel') : config('logging.default'),
            'user_agent' => $request->userAgent(),
            'versions' => [
                'php' => PHP_VERSION,
                'laravel' => App::version(),
                'horizon' => InstalledVersions::getVersion('laravel/horizon'),
                'mysql' => $this->mysqlVersion(),
                'mixpost' => InstalledVersions::getVersion('inovector/mixpost-pro-team'),
                'mixpost_enterprise' => Mixpost::getEnterpriseVersion(),
            ]
        ]);
    }

    protected function getLastScheduleRun(): array
    {
        $lastScheduleRun = Cache::get('mixpost-last-schedule-run');

        if (!$lastScheduleRun) {
            return [
                'variant' => 'error',
                'message' => __('mixpost::system.never_started')
            ];
        }

        if (Carbon::now('UTC')->diffInMinutes($lastScheduleRun) < 10) {
            return [
                'variant' => 'success',
                'message' =>  __('mixpost::system.ran_time_ago', ['time' => Carbon::now('UTC')->diffInMinutes($lastScheduleRun) ])
            ];
        }

        return [
            'variant' => 'warning',
            'message' => __('mixpost::system.ran_time_ago', ['time' => Carbon::now('UTC')->diffInMinutes($lastScheduleRun) ])
        ];
    }

    protected function mysqlVersion(): string
    {
        $results = DB::select('select version() as version');

        return (string)$results[0]->version;
    }

    protected function getScheduledTasks(): Collection
    {
        app()->make(Kernel::class);

        $schedule = app()->make(Schedule::class);

        return collect($schedule->events())
            ->filter(fn($event) => Str::contains($event->description, 'mixpost:'))
            ->map(function (CallbackEvent $event) {
                return [
                    'expression' => $event->expression,
                    'command' => 'mixpost:' . Str::after($event->description, 'mixpost:'),
                ];
            });
    }
}
