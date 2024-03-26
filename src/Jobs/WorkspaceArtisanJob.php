<?php

namespace SaguiAi\MixpostAdapter\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use SaguiAi\MixpostAdapter\Models\Workspace;

class WorkspaceArtisanJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $uniqueFor = 900; // 15 minutes

    public function __construct(
        private readonly Workspace $workspace,
        private readonly string    $command,
    )
    {
    }

    public function handle(): void
    {
        Cache::put('mixpost-last-schedule-run', Carbon::now('utc'));

        $this->workspace->execute(fn() => Artisan::call($this->command));
    }

    public function uniqueId(): string
    {
        return $this->workspace->id . $this->command;
    }
}
