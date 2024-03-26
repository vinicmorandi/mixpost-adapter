<?php

namespace SaguiAi\MixpostAdapter\Actions\Post;

use Illuminate\Support\Facades\Bus;
use SaguiAi\MixpostAdapter\Jobs\AccountPublishPostJob;
use SaguiAi\MixpostAdapter\Models\Post;

class PublishPost
{
    public function __invoke(Post $post): void
    {
        if ($post->isScheduleProcessing()) {
            return;
        }

        $post->setScheduleProcessing();

        $jobs = $post->accounts->map(function ($account) use ($post) {
            return new AccountPublishPostJob($account, $post);
        });

        Bus::batch($jobs)
            ->allowFailures()
            ->finally(function () use ($post) {
                if ($post->hasErrors()) {
                    $post->setFailed();
                    return;
                }

                if ($post->isScheduleProcessing()) {
                    $post->setPublished();
                }
            })
            ->onQueue('publish-post')
            ->dispatch();
    }
}
