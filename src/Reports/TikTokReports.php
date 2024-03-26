<?php

namespace SaguiAi\MixpostAdapter\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use SaguiAi\MixpostAdapter\Abstracts\Report;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\Metric;

class TikTokReports extends Report
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
        $report = Metric::account($account->id)->select(
            DB::raw('SUM(JSON_EXTRACT(data, "$.likes")) as likes'),
            DB::raw('SUM(JSON_EXTRACT(data, "$.views")) as views'),
            DB::raw('SUM(JSON_EXTRACT(data, "$.shares")) as shares'),
            DB::raw('SUM(JSON_EXTRACT(data, "$.comments")) as comments')
        )->when($period, function (Builder $query) use ($period) {
            return $this->queryPeriod($query, $period);
        })->first();

        return [
            'likes' => $report->likes ?? 0,
            'views' => $report->views ?? 0,
            'shares' => $report->shares ?? 0,
            'comments' => $report->comments ?? 0,
        ];
    }
}
