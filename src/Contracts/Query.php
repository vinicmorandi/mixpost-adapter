<?php

namespace SaguiAi\MixpostAdapter\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

interface Query
{
    public static function apply(Request $request): Builder;
}
