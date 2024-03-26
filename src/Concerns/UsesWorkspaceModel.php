<?php

namespace SaguiAi\MixpostAdapter\Concerns;

use SaguiAi\MixpostAdapter\Mixpost;
use SaguiAi\MixpostAdapter\Models\Workspace;

trait UsesWorkspaceModel
{
    public static function getWorkspaceModelClass(): string
    {
        $model = Mixpost::getCustomWorkspaceModel();

        if (!$model) {
            return Workspace::class;
        }

        return $model;
    }
}
