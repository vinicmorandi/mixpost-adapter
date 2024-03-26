<?php

namespace SaguiAi\MixpostAdapter\Builders\Workspace\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Contracts\Filter;

class Exclude implements Filter
{
    public static function apply(Builder $builder, $value): Builder
    {
        return $builder->whereNotIn('uuid', Arr::wrap($value));
    }
}
