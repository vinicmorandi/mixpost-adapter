<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns;

use SaguiAi\MixpostAdapter\Facades\Services;
use SaguiAi\MixpostAdapter\ServiceForm\FacebookServiceForm;

trait ManagesConfig
{
    public static function getApiVersionConfig(): string
    {
        $versions = FacebookServiceForm::versions();

        return Services::get('facebook', 'api_version') ?? current($versions);
    }
}
