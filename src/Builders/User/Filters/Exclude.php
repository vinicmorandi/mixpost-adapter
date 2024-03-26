<?php

namespace SaguiAi\MixpostAdapter\Builders\User\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Contracts\Filter;

class Exclude implements Filter
{
    public static function apply(Builder $builder, $value): Builder
    {
        return $builder->whereNotIn('id', Arr::wrap($value));
    }
}
