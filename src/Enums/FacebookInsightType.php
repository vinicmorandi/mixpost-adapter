<?php

namespace SaguiAi\MixpostAdapter\Enums;

use SaguiAi\MixpostAdapter\Concerns\Enum\EnumHandyMethods;

enum FacebookInsightType: int
{
    use EnumHandyMethods;

    case PAGE_ENGAGED_USERS = 1;
    case PAGE_POST_ENGAGEMENTS = 2;
    case PAGE_POSTS_IMPRESSIONS = 3;
}
