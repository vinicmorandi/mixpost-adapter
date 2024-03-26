<?php

namespace SaguiAi\MixpostAdapter\Builders\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Contracts\Query;
use SaguiAi\MixpostAdapter\Builders\User\Filters\Exclude;
use SaguiAi\MixpostAdapter\Builders\User\Filters\Keyword;

class UserQuery implements Query
{
    use UsesUserModel;

    public static function apply(Request $request): Builder
    {
        $query = self::getUserClass()::query();

        if ($request->has('keyword') && $request->get('keyword')) {
            $query = Keyword::apply($query, $request->get('keyword'));
        }

        if ($request->has('exclude') && $request->get('exclude')) {
            $query = Exclude::apply($query, $request->get('exclude', []));
        }

        return $query;
    }
}
