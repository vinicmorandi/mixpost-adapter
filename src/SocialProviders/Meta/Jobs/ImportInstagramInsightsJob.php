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
use SaguiAi\MixpostAdapter\Enums\InstagramInsightType;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\InstagramInsight;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\InstagramProvider;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

class ImportInstagramInsightsJob implements ShouldQueue, QueueWorkspaceAware
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use UsesSocialProviderManager;
    use HasSocialProviderJobRateLimit;
    use SocialProviderException;

    public $deleteWhenMissingModels = true;

    public Account $account;

    public string $metricType;

    public function __construct(Account $account, string $metricType = 'time_series')
    {
        $this->account = $account;
        $this->metricType = $metricType;
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
        $response = match ($this->metricType) {
            'time_series' => $this->connectProvider($this->account)->getInsightsTimeSeries(),
            'total_value' => $this->connectProvider($this->account)->getInsightsTotalValue(),
        };

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

        $insights = Arr::get($response->context(), 'data', []);

        foreach ($insights as $insight) {
            if ($this->metricType === 'time_series') {
                $this->importInsightsTimeSeries(InstagramInsightType::fromName(Str::upper($insight['name'])), $insight['values']);
            }

//            if ($this->metricType === 'total_value') {
//                $this->importInsightsTotalValue(InstagramInsightType::fromName(Str::upper($insight['name'])), $insight['total_value']);
//            }
        }


//        ImportInstagramInsightsJob::dispatch($this->account, 'total_value')->delay(60 * 60); // 1 hour
    }

    protected function importInsightsTimeSeries(InstagramInsightType $type, array $items): void
    {
        $data = Arr::map($items, function ($item) use ($type) {
            return [
                'workspace_id' => WorkspaceManager::current()->id,
                'account_id' => $this->account->id,
                'type' => $type,
                'date' => Carbon::parse($item['end_time'])->toDateString(),
                'value' => $item['value'],
            ];
        });

        InstagramInsight::upsert($data, ['workspace_id', 'account_id', 'type', 'date'], ['value']);
    }

    protected function importInsightsTotalValue(InstagramInsightType $type, array $items): void
    {
        $data = Arr::map($items, function ($item) use ($type) {
            return [
                'workspace_id' => WorkspaceManager::current()->id,
                'account_id' => $this->account->id,
                'type' => $type,
                'date' => Carbon::today('UTC')->toDateString(),
                'value' => $item['value'],
            ];
        });

        InstagramInsight::upsert($data, ['workspace_id', 'account_id', 'type', 'date'], ['value']);
    }
}
