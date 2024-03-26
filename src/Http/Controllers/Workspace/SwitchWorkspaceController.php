<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Workspace;

use Illuminate\Http\Request;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;

class SwitchWorkspaceController
{
    use UsesAuth;

    public function __invoke(Request $request)
    {
        self::getAuthGuard()->user()->setActiveWorkspace(WorkspaceManager::current());

        if ($request->has('redirect')) {
            return redirect()->route('mixpost.dashboard', ['workspace' => WorkspaceManager::current()->uuid]);
        }
    }
}
