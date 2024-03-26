<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Linkedin;

use SaguiAi\MixpostAdapter\SocialProviders\Linkedin\Concerns\ManagesPageResources;

class LinkedinPageProvider extends LinkedinProvider
{
    use ManagesPageResources;

    public bool $onlyUserAccount = false;
}
