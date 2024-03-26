<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\TikTok\Jobs;

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
use SaguiAi\MixpostAdapter\SocialProviders\TikTok\TikTokProvider;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

class ImportTikTokVideosJob implements ShouldQueue, QueueWorkspaceAware
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
         * @see TikTokProvider
         * @var SocialProviderResponse $response
         */
        $response = $this->connectProvider($this->account)->getVideos(Arr::get($this->params, 'cursor'));

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

        $videos = $this->filter($response->data['videos']);

        $this->import($videos);

        if ($response->data['has_more'] && count($videos) >= 20) {
            ImportTikTokVideosJob::dispatch($this->account, ['cursor' => $response->data['cursor']])->delay(60 * 5);
        }
    }

    protected function filter(array $data): array
    {
        return Arr::where($data, function ($item) {
            return Carbon::createFromTimestamp($item['create_time'], 'UTC')->greaterThan(Carbon::now('UTC')->subDays(90));
        });
    }

    protected function import(array $items): void
    {
        $data = Arr::map($items, function ($item) {
            return [
                'workspace_id' => WorkspaceManager::current()->id,
                'account_id' => $this->account->id,
                'provider_post_id' => $item['id'],
                'content' => json_encode(['text' => $item['video_description']]),
                'metrics' => json_encode([
                    'like_count' => $item['like_count'],
                    'share_count' => $item['share_count'],
                    'comment_count' => $item['comment_count'],
                    'view_count' => $item['view_count'],
                ]),
                'created_at' => Carbon::createFromTimestamp($item['create_time'], 'UTC')->toDateTimeString()
            ];
        });

        ImportedPost::upsert($data, ['workspace_id', 'account_id', 'provider_post_id'], ['content', 'metrics']);
    }
}
