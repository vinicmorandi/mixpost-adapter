<?php

namespace SaguiAi\MixpostAdapter\Concerns;

use SaguiAi\MixpostAdapter\Http\Resources\UserResource;
use SaguiAi\MixpostAdapter\Mixpost;

trait UsesUserResource
{
    public static function getUserResourceClass(): string
    {
        $resource = Mixpost::getCustomUserResource();

        if (!$resource) {
            return UserResource::class;
        }

        return $resource;
    }
}
