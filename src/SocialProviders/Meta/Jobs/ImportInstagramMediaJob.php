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
use SaguiAi\MixpostAdapter\Concerns\Job\HasSocialProviderJobRateLimit;
use SaguiAi\MixpostAdapter\Concerns\Job\SocialProviderException;
use SaguiAi\MixpostAdapter\Concerns\UsesSocialProviderManager;
use SaguiAi\MixpostAdapter\Contracts\QueueWorkspaceAware;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\ImportedPost;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\InstagramProvider;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

class ImportInstagramMediaJob implements ShouldQueue, QueueWorkspaceAware
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
         * @see InstagramProvider
         * @var SocialProviderResponse $response
         */
        $response = $this->connectProvider($this->account)->getMedia($this->params['pagination_after'] ?? '');

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

        $this->import($response->context()['data']);

        if ($after = Arr::get($response->context(), 'paging.cursors.after')) {
            ImportInstagramMediaJob::dispatch($this->account, ['pagination_after' => $after])->delay(5 * 60);
        }
    }

    protected function import(array $items): void
    {
        $data = Arr::map($items, function ($item) {
            return [
                'workspace_id' => WorkspaceManager::current()->id,
                'account_id' => $this->account->id,
                'provider_post_id' => $item['id'],
                'content' => json_encode([
                    'text' => $item['caption'] ?? '', // Reels don't have a caption
                    'media_url' => $item['media_url'] ?? ($item['thumbnail_url'] ?? ''), // Reels don't have a media url, use thumbnail_url instead
                    'is_shared_to_feed' => $item['is_shared_to_feed'] ?? false, // Reels only,
                    'media_product_type' => $item['media_product_type'],
                    'media_type' => $item['media_type'],
                    'permalink' => $item['permalink'],
                    'shortcode' => $item['shortcode'],
                    'username' => $item['username'],
                    'is_comment_enabled' => $item['is_comment_enabled'],

                ]),
                'metrics' => json_encode([
                    'like_count' => $item['like_count'],
                    'comments_count' => $item['comments_count'],
                ]),
                'created_at' => Carbon::parse($item['timestamp'], 'UTC')->toDateString()
            ];
        });

        ImportedPost::upsert($data, ['workspace', 'account_id', 'provider_post_id'], ['content', 'metrics']);
    }
}
