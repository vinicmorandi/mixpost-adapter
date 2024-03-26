<?php

namespace SaguiAi\MixpostAdapter\Reports;

use SaguiAi\MixpostAdapter\Abstracts\Report;
use SaguiAi\MixpostAdapter\Models\Account;

class FacebookGroupReports extends Report
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
//        $report = Metric::account($account->id)->select(
//            DB::raw('SUM(JSON_EXTRACT(data, "$.likes")) as likes'),
//            DB::raw('SUM(JSON_EXTRACT(data, "$.retweets")) as retweets'),
//            DB::raw('SUM(JSON_EXTRACT(data, "$.impressions")) as impressions')
//        )->when($period, function (Builder $query) use ($period) {
//            return $this->queryPeriod($query, $period);
//        })->first();

        return [
            'likes' => $report->likes ?? 0,
            'retweets' => $report->retweets ?? 0,
            'impressions' => $report->impressions ?? 0,
        ];
    }
}
