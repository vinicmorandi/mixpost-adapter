<?php

namespace SaguiAi\MixpostAdapter;

use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Enums\WorkspaceUserRole;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;

class DevTools
{
    use UsesAuth;

    public static function ddIfUser(int $userId, mixed ...$vars): void
    {
        if (self::getAuthGuard()->id() === $userId) {
            dd($vars);
        }
    }

    public static function ddIfAdmin(mixed ...$vars): void
    {
        if (self::getAuthGuard()->user()->isAdmin()) {
            dd($vars);
        }
    }

    public static function ddIfWorkspaceAdmin(mixed ...$vars): void
    {
        if (WorkspaceManager::current() && self::getAuthGuard()
                ->user()
                ->hasWorkspace(
                    WorkspaceManager::current()->id,
                    WorkspaceUserRole::ADMIN
                )) {
            dd($vars);
        }
    }
}
