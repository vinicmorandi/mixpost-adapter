<?php

namespace SaguiAi\MixpostAdapter\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Enums\WorkspaceUserRole;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;

class CheckWorkspaceUser
{
    use UsesAuth;

    public function handle(Request $request, Closure $next, ?string $role = null)
    {
        if (self::getAuthGuard()
            ->user()
            ->hasWorkspace(
                WorkspaceManager::current()->id,
                $role ? WorkspaceUserRole::fromName($role) : null
            )
        ) {
            return $next($request);
        }

        abort(403);
    }
}
