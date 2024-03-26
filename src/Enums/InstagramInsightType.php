<?php

namespace SaguiAi\MixpostAdapter\Enums;

use SaguiAi\MixpostAdapter\Concerns\Enum\EnumHandyMethods;

enum InstagramInsightType: int
{
    use EnumHandyMethods;

    case EMAIL_CONTACTS = 1;
    case FOLLOWER_COUNT = 2;
    case GET_DIRECTIONS_CLICKS = 3;
    case IMPRESSIONS = 4;
    case PHONE_CALL_CLICKS = 5;
    case PROFILE_VIEWS = 6;
    case REACH = 7;
    case TEXT_MESSAGE_CLICKS = 8;
    case WEBSITE_CLICKS = 9;
}
