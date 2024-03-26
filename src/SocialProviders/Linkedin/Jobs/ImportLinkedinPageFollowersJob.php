<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Linkedin\Jobs;

use Illuminate\Support\Carbon;
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
use SaguiAi\MixpostAdapter\Models\Audience;
use SaguiAi\MixpostAdapter\SocialProviders\Linkedin\LinkedinPageProvider;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

class ImportLinkedinPageFollowersJob implements ShouldQueue, QueueWorkspaceAware
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

        if ($retryAfter = $this->rateLimitExpiration()) {
            $this->release($retryAfter);

            return;
        }

        /**
         * @see LinkedinPageProvider
         * @var SocialProviderResponse $response
         */
        $response = $this->connectProvider($this->account)->getFollowerCount();

        if ($response->isUnauthorized()) {
            $this->account->setUnauthorized();
            $this->captureException($response);

            return;
        }

        if ($response->hasExceededRateLimit()) {
            $this->storeRateLimitExceeded($response->retryAfter(), $response->isAppLevel());
            $this->release($response->retryAfter());

            return;
        }

        if ($response->rateLimitAboutToBeExceeded()) {
            $this->storeRateLimitExceeded($response->retryAfter(), $response->isAppLevel());
        }

        if ($response->hasError()) {
            $this->captureException($response);

            return;
        }

        Audience::updateOrCreate([
            'account_id' => $this->account->id,
            'date' => Carbon::now()->utc()->toDateString()
        ], [
            'total' => $response->count ?? 0
        ]);
    }
}
