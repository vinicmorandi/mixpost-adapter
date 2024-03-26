<?php

namespace SaguiAi\MixpostAdapter\Concerns;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Workspace;
use SaguiAi\MixpostAdapter\Scopes\WorkspaceOwnedScope;

trait OwnedByWorkspace
{
    public static function bootOwnedByWorkspace(): void
    {
        static::addGlobalScope(new WorkspaceOwnedScope);

        static::creating(function ($model) {
            if (!$model->workspace_id && !$model->relationLoaded('workspace')) {
                $model->setRelation('workspace', WorkspaceManager::current());
                $model->setAttribute('workspace_id', WorkspaceManager::current()->id);
            }

            return $model;
        });
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'workspace_id');
    }

    public function scopeByWorkspace(Builder $query, int|Model $workspace): void
    {
        $query->withoutWorkspace()
            ->where('workspace_id', $workspace instanceof Model ? $workspace->id : $workspace);
    }
}
