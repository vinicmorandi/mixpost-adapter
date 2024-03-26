<?php

namespace SaguiAi\MixpostAdapter\Builders\Post\Filters;

use Illuminate\Database\Eloquent\Builder;
use SaguiAi\MixpostAdapter\Contracts\Filter;

class PostScheduledAtPeriod implements Filter
{
    public static function apply(Builder $builder, $value): Builder
    {
        return $builder->whereDate('scheduled_at', '>=', $value['scheduled_at_period_start'])
            ->whereDate('scheduled_at', '<=', $value['scheduled_at_period_end']);
    }
}
