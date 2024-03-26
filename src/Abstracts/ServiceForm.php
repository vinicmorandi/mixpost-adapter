<?php

namespace SaguiAi\MixpostAdapter\Abstracts;

use SaguiAi\MixpostAdapter\Contracts\ServiceForm as ServiceFormRulesInterface;

abstract class ServiceForm implements ServiceFormRulesInterface
{
    /**
     * The attributes that should be considered as an additional configuration.
     */
    public static array $configs = [];
}
