<?php

namespace SaguiAi\MixpostAdapter\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use SaguiAi\MixpostAdapter\Concerns\Job\HasSocialProviderJobRateLimit;
use SaguiAi\MixpostAdapter\Concerns\Job\SocialProviderException;
use SaguiAi\MixpostAdapter\Concerns\UsesSocialProviderManager;
use SaguiAi\MixpostAdapter\Contracts\QueueWorkspaceAware;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

class CheckAndRefreshAccountTokenJob implements ShouldQueue, QueueWorkspaceAware
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use UsesSocialProviderManager;
    use HasSocialProviderJobRateLimit;
    use SocialProviderException;

    public $deleteWhenMissingModels = true;

    public Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function handle(): void
    {
        if ($this->account->isUnauthorized()) {
            return;
        }

        $connection = $this->connectProvider($this->account);

        if (!$connection->hasRefreshToken()) {
            return;
        }

        if (!method_exists($connection, 'refreshToken')) {
            return;
        }

        if (!$connection->tokenIsAboutToExpire()) {
            return;
        }

        /**
         * @var SocialProviderResponse $response
         **/
        $response = $connection->refreshToken();

        if ($response->isUnauthorized()) {
            $this->account->setUnauthorized();

            return;
        }

        if ($response->hasError()) {
            $this->captureException($response);

            return;
        }

        $this->account->updateAccessToken(
            array_merge(
                $this->account->access_token->toArray(),
                $response->context()
            )
        );
    }
}
