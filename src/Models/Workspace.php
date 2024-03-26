<?php

namespace SaguiAi\MixpostAdapter\Models;

use Closure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use SaguiAi\MixpostAdapter\Concerns\Model\HasUuid;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Enums\WorkspaceUserRole;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;

class Workspace extends Model
{
    use HasFactory;
    use HasUuid;
    use UsesUserModel;

    public $table = 'mixpost_workspaces';

    protected $fillable = [
        'uuid',
        'name',
        'hex_color'
    ];

    public function execute(Closure $closure): mixed
    {
        $originalWorkspace = WorkspaceManager::current();

        WorkspaceManager::setCurrent($this);

        $result = $closure($this);

        if ($originalWorkspace) {
            WorkspaceManager::setCurrent($originalWorkspace);
        }

        return $result;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(self::getUserClass(), 'mixpost_workspace_user', 'workspace_id', 'user_id')
            ->withPivot('role', 'joined');
    }

    public function scopeRecentlyUpdated($query): void
    {
        $query->latest('updated_at');
    }

    public function attachUser(int $id = null, WorkspaceUserRole $role = WorkspaceUserRole::MEMBER): void
    {
        $this->users()->attach($id, [
            'role' => $role,
            'joined' => Carbon::now('UTC')
        ]);
    }

    public function valid(): bool
    {
        return true;
    }
}
