<?php

namespace SaguiAi\MixpostAdapter\Builders\Post\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Contracts\Filter;

class PostKeyword implements Filter
{
    public static function apply(Builder $builder, $value): Builder
    {
        return $builder->whereHas('versions', function ($query) use ($value) {
            $query->whereRaw('LOWER(content->>"$[*].body") LIKE  "%' . Str::lower($value) . '%"');
        });
    }
}
