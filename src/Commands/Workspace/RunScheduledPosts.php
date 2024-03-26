<?php

namespace SaguiAi\MixpostAdapter\Commands\Workspace;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use SaguiAi\MixpostAdapter\Actions\Post\PublishPost;
use SaguiAi\MixpostAdapter\Enums\PostScheduleStatus;
use SaguiAi\MixpostAdapter\Enums\PostStatus;
use SaguiAi\MixpostAdapter\Models\Post;

class RunScheduledPosts extends Command
{
    public $signature = 'mixpost:run-scheduled-posts';

    public $description = 'Scan & run scheduled posts';

    public function handle(): int
    {
        Post::with('accounts')
            ->where('status', PostStatus::SCHEDULED->value)
            ->where('schedule_status', PostScheduleStatus::PENDING->value)
            ->where('scheduled_at', '<=', Carbon::now()->utc())
            ->each(function (Post $post) {
                (new PublishPost())($post);
            });

        return self::SUCCESS;
    }
}
