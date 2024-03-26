<?php

namespace SaguiAi\MixpostAdapter\Concerns\Job;

use SaguiAi\MixpostAdapter\Support\Log;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

trait SocialProviderException
{
    public function captureException(SocialProviderResponse $response): void
    {
        Log::error($this->job->payload()['displayName'], array_merge($response->context(), ['payload' => $this->job->payload()]));
    }
}
