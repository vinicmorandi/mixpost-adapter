<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Mastodon\Jobs;

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
use SaguiAi\MixpostAdapter\SocialProviders\Mastodon\MastodonProvider;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

class ImportMastodonPostsJob implements ShouldQueue, QueueWorkspaceAware
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
         * @see MastodonProvider
         * @var SocialProviderResponse $response
         */
        $response = $this->connectProvider($this->account)->getUserStatuses($this->account->provider_id, $this->params['max_id'] ?? '');

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

        $posts = $this->filterPosts($response->data);

        $this->import($posts);

        // If more than 40(limit of page items), there is a probability that there are others.
        if (count($posts) >= 40) {
            ImportMastodonPostsJob::dispatch($this->account, ['max_id' => Arr::last($posts)['id']])->delay(60 * 15);
        }
    }

    protected function filterPosts(array $data): array
    {
        return Arr::where($data, function ($item) {
            return Carbon::parse($item['created_at'], 'UTC')->greaterThan(Carbon::now('UTC')->subDays(90));
        });
    }

    protected function import(array $items): void
    {
        $data = Arr::map($items, function ($item) {
            return [
                'workspace_id' => WorkspaceManager::current()->id,
                'account_id' => $this->account->id,
                'provider_post_id' => $item['id'],
                'content' => json_encode(['text' => $item['content']]),
                'metrics' => json_encode([
                    'replies' => $item['replies_count'],
                    'reblogs' => $item['reblogs_count'],
                    'favourites' => $item['favourites_count'],
                ]),
                'created_at' => Carbon::parse($item['created_at'], 'UTC')->toDateTimeString()
            ];
        });

        ImportedPost::upsert($data, ['workspace_id', 'account_id', 'provider_post_id'], ['content', 'metrics']);
    }
}
