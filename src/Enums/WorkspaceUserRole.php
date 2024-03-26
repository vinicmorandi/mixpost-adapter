<?php

namespace SaguiAi\MixpostAdapter\Enums;

use SaguiAi\MixpostAdapter\Concerns\Enum\EnumHandyMethods;

enum WorkspaceUserRole: string
{
    use EnumHandyMethods;

    case ADMIN = 'admin';
    case MEMBER = 'member';
}
