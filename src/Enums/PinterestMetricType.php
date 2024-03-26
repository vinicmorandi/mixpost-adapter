<?php

namespace SaguiAi\MixpostAdapter\Enums;

use SaguiAi\MixpostAdapter\Concerns\Enum\EnumHandyMethods;

enum PinterestMetricType: string
{
    use EnumHandyMethods;

    case SAVE_RATE = 'save_rate';
    case PIN_CLICK = 'pin_click';
    case IMPRESSION = 'impression';
    case OUTBOUND_CLICK = 'outbound_click';
    case SAVE = 'save';
}
