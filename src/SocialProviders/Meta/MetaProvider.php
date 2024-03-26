<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta;

use Illuminate\Http\Request;
use SaguiAi\MixpostAdapter\Abstracts\SocialProvider;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns\ManagesConfig;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns\ManagesMetaResources;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns\ManagesRateLimit;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns\MetaOauth;

class MetaProvider extends SocialProvider
{
    use ManagesConfig;
    use ManagesRateLimit;
    use MetaOauth;
    use ManagesMetaResources;

    public array $callbackResponseKeys = ['code'];

    protected string $apiVersion;
    protected string $apiUrl = 'https://graph.facebook.com';
    protected string $scope;

    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = [])
    {
        $this->setApiVersion();

        $this->setScope();

        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $values);
    }

    protected function setApiVersion(): void
    {
        $this->apiVersion = $this->getApiVersionConfig();
    }

    protected function setScope(): void
    {
        $this->scope = implode(',', $this->getSupportedScopeList());
    }

    public function getSupportedScopeList(): array
    {
        return match ($this->apiVersion) {
            'v16.0' => [
                'pages_show_list',
                'read_insights',
                'pages_manage_posts',
                'publish_to_groups',
                'instagram_basic',
                'instagram_content_publish',
                'instagram_manage_insights'
            ],
            'v17.0', 'v18.0' => [
                'business_management',
                'pages_show_list',
                'read_insights',
                'pages_manage_posts',
                'publish_to_groups',
                'instagram_basic',
                'instagram_content_publish',
                'instagram_manage_insights'
            ],
            default => [
                'business_management',
                'pages_show_list',
                'read_insights',
                'pages_manage_posts',
                'instagram_basic',
                'instagram_content_publish',
                'instagram_manage_insights'
            ]
        };
    }

    public function getAuthUrl(): string
    {
        return '';
    }

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        return '#';
    }

    public static function mapErrorMessage(string $key): string
    {
        return match ($key) {
            'access_token_expired' => __('mixpost::account.access_token_expired'),
            'no_media_selected' => __('mixpost::service.post.no_media_selected'),
            'reel_only_video_allowed' => __('mixpost::service.meta.reel_only_video_allowed'),
            'reel_supports_one_video' => __('mixpost::service.meta.reel_supports_one_video'),
            'story_single_media_limit' => __('mixpost::service.meta.story_single_media_limit'),
            'publication_video_expired' => __('mixpost::service.meta.error.publication_video_expired'),
            'session_expired' => __('mixpost::service.meta.error.session_expired'),
            'media_already_published' => __('mixpost::service.meta.error.media_already_published'),
            '100' => __('mixpost::service.meta.error.required_param_missing'),
            '1363040' => __('mixpost::media.aspect_ratio_range', ['min' => '16x9', 'max' => '9x16']),
            '1363127' => __('mixpost::media.resolution_range', ['min' => '540', 'max' => '960', 'recommended_min' => '1080', 'recommended_max' => '1920']),
            '1363128' => __('mixpost::media.duration_range', ['min' => '3', 'max' => '90']),
            '1363129' => __('mixpost::media.frame_rate_range', ['min' => '24', 'max' => '60']),
            'error_upload_video' => __('mixpost::error.error_upload_video'),
            'request_timeout' => __('mixpost::error.request_timeout'),
            'unknown_error' => __('mixpost::error.unknown_error'),
            default => $key,
        };
    }
}
