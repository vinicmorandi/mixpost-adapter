<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Twitter\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Concerns\Job\HasSocialProviderJobRateLimit;
use SaguiAi\MixpostAdapter\Concerns\Job\SocialProviderException;
use SaguiAi\MixpostAdapter\Concerns\UsesSocialProviderManager;
use SaguiAi\MixpostAdapter\Contracts\QueueWorkspaceAware;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\ImportedPost;
use SaguiAi\MixpostAdapter\SocialProviders\Twitter\TwitterProvider;

class ImportTwitterPostsJob implements ShouldQueue, QueueWorkspaceAware
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use UsesSocialProviderManager;
    use HasSocialProviderJobRateLimit;
    use SocialProviderException;

    public $deleteWhenMissingModels = true;

    public Account $account;
    public array $params;

    public function __construct(Account $account, array $params = [])
    {
        $this->account = $account;
        $this->params = $params;
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
         * @var TwitterProvider $provider
         */
        $provider = $this->connectProvider($this->account);

        // Twitter `free` Tier doesn't support endpoints from this job
        if ($provider->getTier() === 'free') {
            return;
        }

        $response = $provider->getUserTweetTimeline($this->account->provider_id, $this->params['pagination_next_token'] ?? '');

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

        $this->import($response->data);

        if (isset($response->meta->next_token)) {
            ImportTwitterPostsJob::dispatch($this->account, ['pagination_next_token' => $response->meta->next_token])->delay(60);
        }
    }

    protected function import(array $items): void
    {
        $data = Arr::map($items, function ($item) {
            return [
                'workspace_id' => WorkspaceManager::current()->id,
                'account_id' => $this->account->id,
                'provider_post_id' => $item->id,
                'content' => json_encode(['text' => $item->text]),
                'metrics' => json_encode([
                    'user_profile_clicks' => $item->public_metrics->user_profile_clicks ?? 0,
                    'impressions' => $item->public_metrics->impression_count ?? 0,
                    'likes' => $item->public_metrics->like_count ?? 0,
                    'replies' => $item->public_metrics->reply_count ?? 0,
                    'retweets' => $item->public_metrics->retweet_count ?? 0,
                ]),
                'created_at' => Carbon::parse($item->created_at, 'UTC')->toDateTimeString()
            ];
        });

        ImportedPost::upsert($data, ['workspace_id', 'account_id', 'provider_post_id'], ['content', 'metrics']);
    }
}
