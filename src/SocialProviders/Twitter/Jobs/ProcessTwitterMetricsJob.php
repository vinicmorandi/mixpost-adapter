<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Twitter\Jobs;

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

class ProcessTwitterMetricsJob implements ShouldQueue, QueueWorkspaceAware
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteWhenMissingModels = true;

    public Account $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function handle()
    {
        $items = ImportedPost::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.likes")) as likes'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.replies")) as replies'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.retweets")) as retweets'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.impressions")) as impressions'))
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
                    'replies' => $item->replies,
                    'retweets' => $item->retweets,
                    'impressions' => $item->impressions,
                ])
            ];
        });

        Metric::upsert($data->toArray(), ['data'], ['workspace_id', 'account_id', 'date']);
    }
}
