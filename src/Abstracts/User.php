<?php

namespace SaguiAi\MixpostAdapter\Abstracts;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use SaguiAi\MixpostAdapter\Concerns\Model\TwoFactorAuthenticatable;
use SaguiAi\MixpostAdapter\Concerns\Model\UserHasSettings;
use SaguiAi\MixpostAdapter\Concerns\Model\UserHasWorkspaces;
use SaguiAi\MixpostAdapter\Models\Admin;
use SaguiAi\MixpostAdapter\Models\Workspace;

abstract class User extends Authenticatable
{
    use UserHasWorkspaces;
    use UserHasSettings;
    use TwoFactorAuthenticatable;

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->admin()->exists();
    }

    public function setActiveWorkspace(Workspace $workspace): void
    {
        $this->settings()->updateOrCreate(
            [
                'name' => 'active_workspace',
                'user_id' => $this->id
            ],
            ['payload' => $workspace->id]
        );
    }

    public function getActiveWorkspace()
    {
        $workspaceId = $this->settings()
            ->where('name', 'active_workspace')
            ->value('payload');

        if (!$workspaceId) {
            return null;
        }

        return $this->workspaces()->where('workspace_id', $workspaceId)->first();
    }
}
