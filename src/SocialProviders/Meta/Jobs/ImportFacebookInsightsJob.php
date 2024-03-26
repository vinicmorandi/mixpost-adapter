<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Concerns\Job\HasSocialProviderJobRateLimit;
use SaguiAi\MixpostAdapter\Concerns\Job\SocialProviderException;
use SaguiAi\MixpostAdapter\Concerns\UsesSocialProviderManager;
use SaguiAi\MixpostAdapter\Contracts\QueueWorkspaceAware;
use SaguiAi\MixpostAdapter\Enums\FacebookInsightType;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\FacebookInsight;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\FacebookPageProvider;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

class ImportFacebookInsightsJob implements ShouldQueue, QueueWorkspaceAware
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
         * @see FacebookPageProvider
         * @var SocialProviderResponse $response
         */
        $response = $this->connectProvider($this->account)->getPageInsights();

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

        $insights = $response->context()['data'];

        foreach ($insights as $insight) {
            $this->importInsights(FacebookInsightType::fromName(Str::upper($insight['name'])), $insight['values']);
        }
    }

    protected function importInsights(FacebookInsightType $type, array $items): void
    {
        $data = Arr::map($items, function ($item) use ($type) {
            return [
                'workspace_id' => WorkspaceManager::current()->id,
                'account_id' => $this->account->id,
                'type' => $type,
                'date' => Carbon::parse($item['end_time'], 'UTC')->toDateString(),
                'value' => $item['value'],
            ];
        });

        FacebookInsight::upsert($data, ['workspace_id', 'account_id', 'type', 'date'], ['value']);
    }
}
