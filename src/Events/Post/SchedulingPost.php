<?php

namespace SaguiAi\MixpostAdapter\Events\Post;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Post;
use SaguiAi\MixpostAdapter\Models\Workspace;

class SchedulingPost
{
    use Dispatchable, SerializesModels;

    public ?Workspace $workspace;
    public Post $post;
    public Request $request;

    public function __construct(Post $post, Request $request)
    {
        $this->workspace = WorkspaceManager::current();
        $this->post = $post;
        $this->request = $request;
    }
}
