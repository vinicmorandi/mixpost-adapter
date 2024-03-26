<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\TikTok\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use SaguiAi\MixpostAdapter\Contracts\QueueWorkspaceAware;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\ImportedPost;
use SaguiAi\MixpostAdapter\Models\Metric;

class ProcessTikTokMetricsJob implements ShouldQueue, QueueWorkspaceAware
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    public Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function handle(): void
    {
        $items = ImportedPost::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.like_count")) as likes'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.share_count")) as shares'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.comment_count")) as comments'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.view_count")) as views'))
            ->where('account_id', $this->account->id)
            ->groupBy('date')
            ->cursor();

        $data = $items->map(function ($item) {
            return [
                'workspace_id' => WorkspaceManager::current()->id,
                'account_id' => $this->account->id,
                'date' => $item->date,
                'data' => json_encode([
                    'likes' => $item->likes,
                    'shares' => $item->shares,
                    'comments' => $item->comments,
                    'views' => $item->views,
                ])
            ];
        });

        Metric::upsert($data->toArray(), ['data'], ['workspace_id', 'account_id', 'date']);
    }
}
