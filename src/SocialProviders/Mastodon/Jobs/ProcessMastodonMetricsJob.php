<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Mastodon\Jobs;

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

class ProcessMastodonMetricsJob implements ShouldQueue, QueueWorkspaceAware
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
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.replies")) as replies'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.reblogs")) as reblogs'),
            DB::raw('SUM(JSON_EXTRACT(metrics, "$.favourites")) as favourites'))
            ->where('account_id', $this->account->id)
            ->groupBy('date')
            ->cursor();

        $data = $items->map(function ($item) {
            return [
                'workspace_id' => WorkspaceManager::current()->id,
                'account_id' => $this->account->id,
                'date' => $item->date,
                'data' => json_encode([
                    'replies' => $item->replies,
                    'reblogs' => $item->reblogs,
                    'favourites' => $item->favourites,
                ])
            ];
        });

        Metric::upsert($data->toArray(), ['data'], ['workspace_id', 'account_id', 'date']);
    }
}
