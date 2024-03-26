<?php

namespace SaguiAi\MixpostAdapter\Reports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use SaguiAi\MixpostAdapter\Abstracts\Report;
use SaguiAi\MixpostAdapter\Enums\InstagramInsightType;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\InstagramInsight;
use SaguiAi\MixpostAdapter\Models\Metric;

class InstagramReports extends Report
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
        $reports = InstagramInsight::account($account->id)
            ->select('type', DB::raw('SUM(value) as total'))
            ->when($period, function (Builder $query) use ($period) {
                return $this->queryPeriod($query, $period);
            })
            ->groupBy('type')
            ->get();

        $processedMetricsFromImportedPosts = Metric::account($account->id)->select(
            DB::raw('SUM(JSON_EXTRACT(data, "$.likes")) as likes'),
            DB::raw('SUM(JSON_EXTRACT(data, "$.comments")) as comments')
        )->when($period, function (Builder $query) use ($period) {
            return $this->queryPeriod($query, $period);
        })->first();

        $impressions = (int)$reports->where('type', InstagramInsightType::IMPRESSIONS)->value('total', 0);
        $emailContacts = (int)$reports->where('type', InstagramInsightType::EMAIL_CONTACTS)->value('total', 0);
        $followerCount = (int)$reports->where('type', InstagramInsightType::FOLLOWER_COUNT)->value('total', 0);
        $phoneCallClicks = (int)$reports->where('type', InstagramInsightType::PHONE_CALL_CLICKS)->value('total', 0);
        $profileViews = (int)$reports->where('type', InstagramInsightType::PROFILE_VIEWS)->value('total', 0);
        $getDirectionsClicks = (int)$reports->where('type', InstagramInsightType::GET_DIRECTIONS_CLICKS)->value('total', 0);
        $reach = (int)$reports->where('type', InstagramInsightType::REACH)->value('total', 0);
        $textMessageClicks = (int)$reports->where('type', InstagramInsightType::TEXT_MESSAGE_CLICKS)->value('total', 0);
        $websiteClicks = (int)$reports->where('type', InstagramInsightType::WEBSITE_CLICKS)->value('total', 0);

        $engagement = $emailContacts +
            $phoneCallClicks +
            $getDirectionsClicks +
            $textMessageClicks +
            $websiteClicks;

        return [
            'likes' => (int)$processedMetricsFromImportedPosts->likes ?? 0,
            'comments' => (int)$processedMetricsFromImportedPosts->comments ?? 0,
            'email_contacts' => $emailContacts,
            'follower_count' => $followerCount,
            'get_directions_click' => $getDirectionsClicks,
            'impressions' => $impressions,
            'phone_call_clicks' => $phoneCallClicks,
            'profile_views' => $profileViews,
            'reach' => $reach,
            'text_message_clicks' => $textMessageClicks,
            'website_clicks' => $websiteClicks,
            'engagement' => $engagement,
            'engagement_rate' => $impressions ? number_format(($engagement / $impressions) * 100, 2) : 0
        ];
    }
}
