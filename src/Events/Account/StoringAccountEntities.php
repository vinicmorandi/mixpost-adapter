<?php

namespace SaguiAi\MixpostAdapter\Events\Account;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Workspace;

class StoringAccountEntities
{
    use Dispatchable, SerializesModels;

    public ?Workspace $workspace;
    public string $provider;
    public array $items;

    public function __construct(string $provider, array $items)
    {
        $this->workspace = WorkspaceManager::current();
        $this->provider = $provider;
        $this->items = $items;
    }
}
