<?php

namespace SaguiAi\MixpostAdapter\Enums;

enum PostScheduleStatus: int
{
    case PENDING = 0;
    case PROCESSING = 1;
    case PROCESSED = 2;
}
