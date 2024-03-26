<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use SaguiAi\MixpostAdapter\Concerns\Job\HasSocialProviderJobRateLimit;
use SaguiAi\MixpostAdapter\Concerns\Job\SocialProviderException;
use SaguiAi\MixpostAdapter\Concerns\UsesSocialProviderManager;
use SaguiAi\MixpostAdapter\Contracts\QueueWorkspaceAware;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\InstagramProvider;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

/// TODO: Integrate this Job
class UpdateInstagramPostShortcodeJob implements ShouldQueue, ShouldBeUnique, QueueWorkspaceAware
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use UsesSocialProviderManager;
    use HasSocialProviderJobRateLimit;
    use SocialProviderException;

    public $deleteWhenMissingModels = true;

    public AccountResource $account;

    public function __construct(AccountResource $account)
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
         * @see InstagramProvider
         * @var SocialProviderResponse $response
         */
        $response = $this->connectProvider($this->account->resource)->getPost($this->account->pivot->provider_post_id, 'shortcode');

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

        DB::table('mixpost_post_accounts')
            ->where('account_id', $this->account->id)
            ->where('provider_post_id', $this->account->pivot->provider_post_id)
            ->update([
                'data' => [
                    'shortcode' => $response->shortcode
                ]
            ]);
    }

    public function uniqueId(): string
    {
        return $this->account->id . $this->account->pivot->provider_post_id;
    }
}
