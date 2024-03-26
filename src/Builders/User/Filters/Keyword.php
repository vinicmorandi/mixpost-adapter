<?php

namespace SaguiAi\MixpostAdapter\Builders\User\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Contracts\Filter;

class Keyword implements Filter
{
    public static function apply(Builder $builder, $value): Builder
    {
        $lowerValue = Str::lower($value);

        return $builder->where(function (Builder $query) use ($lowerValue) {
            $query->where('name', 'LIKE', '%' . $lowerValue . '%')
                ->orWhere('email', 'LIKE', '%' . $lowerValue . '%');
        });
    }
}
