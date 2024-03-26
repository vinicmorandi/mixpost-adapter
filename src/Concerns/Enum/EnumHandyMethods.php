<?php

namespace SaguiAi\MixpostAdapter\Concerns\Enum;

trait EnumHandyMethods
{
    public static function fromName(string $name)
    {
        return constant("self::$name");
    }
}
