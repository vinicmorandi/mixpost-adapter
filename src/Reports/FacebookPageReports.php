<?php

namespace SaguiAi\MixpostAdapter\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use SaguiAi\MixpostAdapter\Abstracts\Report;
use SaguiAi\MixpostAdapter\Enums\FacebookInsightType;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\FacebookInsight;

class FacebookPageReports extends Report
{
    public function __invoke(Account $account, string $period): array
    {
        return [
            'metrics' => $this->metrics($account, $period),
            'audience' => $this->audience($account, $period)
        ];
    }

    protected function metrics(Account $account, string $period): array
    {
        $reports = FacebookInsight::account($account->id)
            ->select('type', DB::raw('SUM(value) as total'))
            ->when($period, function (Builder $query) use ($period) {
                return $this->queryPeriod($query, $period);
            })
            ->groupBy('type')
            ->get();

        return [
            'page_engaged_users' => $reports->where('type', FacebookInsightType::PAGE_ENGAGED_USERS)->value('total', 0),
            'page_post_engagements' => $reports->where('type', FacebookInsightType::PAGE_POST_ENGAGEMENTS)->value('total', 0),
            'page_posts_impressions' => $reports->where('type', FacebookInsightType::PAGE_POSTS_IMPRESSIONS)->value('total', 0)
        ];
    }
}
