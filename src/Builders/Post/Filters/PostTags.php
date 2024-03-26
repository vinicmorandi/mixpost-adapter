<?php

namespace SaguiAi\MixpostAdapter\Builders\Post\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Contracts\Filter;

class PostTags implements Filter
{
    public static function apply(Builder $builder, $value): Builder
    {
        return $builder->whereHas('tags', function ($query) use ($value) {
            $query->whereIn('tag_id', Arr::wrap($value));
        });
    }
}
