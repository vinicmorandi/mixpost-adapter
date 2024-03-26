<?php

namespace SaguiAi\MixpostAdapter\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use SaguiAi\MixpostAdapter\Abstracts\Report;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\PinterestAnalytic;

class PinterestReports extends Report
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
        $report = PinterestAnalytic::account($account->id)
            ->select(
                DB::raw('SUM(JSON_EXTRACT(metrics, "$.save")) as save'),
                DB::raw('SUM(JSON_EXTRACT(metrics, "$.pin_click")) as pin_click'),
                DB::raw('SUM(JSON_EXTRACT(metrics, "$.save_rate")) as save_rate'),
                DB::raw('SUM(JSON_EXTRACT(metrics, "$.impression")) as impression'),
                DB::raw('SUM(JSON_EXTRACT(metrics, "$.outbound_click")) as outbound_click'),
            )
            ->when($period, function (Builder $query) use ($period) {
                return $this->queryPeriod($query, $period);
            })
            ->first();

        return [
            'save' => $report->save ?? 0,
            'pin_click' => $report->pin_click ?? 0,
            'save_rate' => $report->save_rate ?? 0,
            'impression' => $report->impression ?? 0,
            'outbound_click' => $report->outbound_click ?? 0,
        ];
    }
}
