<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Workspace;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use SaguiAi\MixpostAdapter\Contracts\ProviderReports;
use SaguiAi\MixpostAdapter\Facades\WorkspaceManager;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Reports\FacebookGroupReports;
use SaguiAi\MixpostAdapter\Reports\FacebookPageReports;
use SaguiAi\MixpostAdapter\Reports\InstagramReports;
use SaguiAi\MixpostAdapter\Reports\LinkedinPageReports;
use SaguiAi\MixpostAdapter\Reports\LinkedinReports;
use SaguiAi\MixpostAdapter\Reports\MastodonReports;
use SaguiAi\MixpostAdapter\Reports\PinterestReports;
use SaguiAi\MixpostAdapter\Reports\TikTokReports;
use SaguiAi\MixpostAdapter\Reports\TwitterReports;
use SaguiAi\MixpostAdapter\Reports\YoutubeReports;

class Reports extends FormRequest
{
    public function rules(): array
    {
        return [
            'account_id' => ['required', 'integer', WorkspaceManager::existsRule('mixpost_accounts', 'id')],
            'period' => ['required', 'string', Rule::in(['7_days', '30_days', '90_days'])]
        ];
    }

    public function handle(): array
    {
        $account = Account::find($this->get('account_id'));

        $providerReports = match ($account->provider) {
            'twitter' => TwitterReports::class,
            'facebook_page' => FacebookPageReports::class,
            'facebook_group' => FacebookGroupReports::class,
            'instagram' => InstagramReports::class,
            'mastodon' => MastodonReports::class,
            'pinterest' => PinterestReports::class,
            'linkedin' => LinkedinReports::class,
            'linkedin_page' => LinkedinPageReports::class,
            'tiktok' => TikTokReports::class,
            'youtube' => YoutubeReports::class,
            default => null
        };

        if (!$providerReports) {
            return [];
        }

        $providerReports = (new $providerReports());

        if (!$providerReports instanceof ProviderReports) {
            throw new \Exception('The provider reports must be an instance of ProviderReports');
        }

        return $providerReports($account, $this->get('period', ''));
    }
}
