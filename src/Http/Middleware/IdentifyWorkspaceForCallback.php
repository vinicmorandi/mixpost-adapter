<?php

namespace SaguiAi\MixpostAdapter\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;

class IdentifyWorkspaceForCallback
{
    public function handle(Request $request, Closure $next)
    {
        if (WorkspaceManager::loadByUuid((string)$request->get('state'))) {
            return $next($request);
        }

        abort(404);
    }
}
