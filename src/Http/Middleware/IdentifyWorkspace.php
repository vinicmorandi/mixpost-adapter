<?php

namespace SaguiAi\MixpostAdapter\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;

class IdentifyWorkspace
{
    public function handle(Request $request, Closure $next)
    {
        if (WorkspaceManager::loadByUuid($request->route('workspace'))) {
            return $next($request);
        }

        abort(404);
    }
}
