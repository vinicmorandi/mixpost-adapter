<?php

namespace SaguiAi\MixpostAdapter\Events\Account;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use SaguiAi\MixpostAdapter\Contracts\SocialProvider;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Workspace;

class AddingAccount
{
    use Dispatchable, SerializesModels;

    public ?Workspace $workspace;
    public SocialProvider $provider;

    public function __construct(SocialProvider $provider)
    {
        $this->workspace = WorkspaceManager::current();
        $this->provider = $provider;
    }
}
