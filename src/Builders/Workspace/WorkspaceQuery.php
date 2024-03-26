<?php

namespace SaguiAi\MixpostAdapter\Builders\Workspace;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use SaguiAi\MixpostAdapter\Contracts\Query;
use SaguiAi\MixpostAdapter\Models\Workspace;
use SaguiAi\MixpostAdapter\Builders\Workspace\Filters\Exclude;
use SaguiAi\MixpostAdapter\Builders\Workspace\Filters\Keyword;

class WorkspaceQuery implements Query
{
    public static function apply(Request $request): Builder
    {
        $query = Workspace::query();

        if ($request->has('keyword') && $request->get('keyword')) {
            $query = Keyword::apply($query, $request->get('keyword'));
        }

        if ($request->has('exclude') && $request->get('exclude')) {
            $query = Exclude::apply($query, $request->get('exclude', []));
        }

        return $query;
    }
}
