<?php

namespace SaguiAi\MixpostAdapter\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\Audience;
use SaguiAi\MixpostAdapter\Models\FacebookInsight;
use SaguiAi\MixpostAdapter\Models\HashtagGroup;
use SaguiAi\MixpostAdapter\Models\ImportedPost;
use SaguiAi\MixpostAdapter\Models\InstagramInsight;
use SaguiAi\MixpostAdapter\Models\Media;
use SaguiAi\MixpostAdapter\Models\Metric;
use SaguiAi\MixpostAdapter\Models\PinterestAnalytic;
use SaguiAi\MixpostAdapter\Models\Post;
use SaguiAi\MixpostAdapter\Models\PostingSchedule;
use SaguiAi\MixpostAdapter\Models\Tag;
use SaguiAi\MixpostAdapter\Models\Template;
use SaguiAi\MixpostAdapter\Models\Variable;

class DeleteWorkspaceDataJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int    $workspaceId,
        private readonly string $workspaceUuid
    )
    {
    }

    public function handle(): void
    {
        Post::withoutWorkspace()->where('workspace_id', $this->workspaceId)->forceDelete();
        PostingSchedule::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();
        Account::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();
        Tag::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();
        HashtagGroup::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();
        Variable::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();
        Template::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();
        ImportedPost::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();
        FacebookInsight::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();
        InstagramInsight::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();
        PinterestAnalytic::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();
        Metric::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();
        Audience::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();

        // Delete Media
        Media::withoutWorkspace()
            ->where('workspace_id', $this->workspaceId)
            ->select('disk')
            ->groupBy('disk')
            ->cursor()
            ->each(function ($item) {
                try {
                    Storage::disk($item->disk)->deleteDirectory($this->workspaceUuid);
                } catch (\Exception $exception) {

                }
            });

        Media::withoutWorkspace()->where('workspace_id', $this->workspaceId)->delete();
    }
}
