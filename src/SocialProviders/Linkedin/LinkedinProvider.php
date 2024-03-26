<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Linkedin;

use Illuminate\Http\Request;
use SaguiAi\MixpostAdapter\Abstracts\SocialProvider;
use SaguiAi\MixpostAdapter\Facades\Services;
use SaguiAi\MixpostAdapter\Http\Resources\AccountResource;
use SaguiAi\MixpostAdapter\SocialProviders\Linkedin\Concerns\ManagesOAuth;
use SaguiAi\MixpostAdapter\SocialProviders\Linkedin\Concerns\ManagesRateLimit;
use SaguiAi\MixpostAdapter\SocialProviders\Linkedin\Concerns\ManagesResources;

class LinkedinProvider extends SocialProvider
{
    use ManagesRateLimit;
    use ManagesOAuth;
    use ManagesResources;

    public array $callbackResponseKeys = ['code'];

    protected array $scope;

    public string $apiVersion = 'v2';
    public string $apiUrl = 'https://api.linkedin.com';

    public function __construct(Request $request, string $clientId, string $clientSecret, string $redirectUrl, array $values = [])
    {
        $this->setScope();

        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $values);
    }

    public static function getProduct(): string
    {
        return Services::get('linkedin', 'product') ?? 'sign_share';
    }

    public static function hasCommunityManagementProduct(): bool
    {
        return self::getProduct() === 'community_management';
    }

    public static function hasSignInOpenIdShareProduct(): bool
    {
        return self::getProduct() === 'sign_open_id_share';
    }

    protected function setScope(): void
    {
        $this->scope = match (self::getProduct()) {
            'sign_share' => ['r_liteprofile', 'r_emailaddress', 'w_member_social'],
            'sign_open_id_share' => ['openid', 'profile', 'w_member_social'],
            'community_management' => ['w_member_social', 'r_basicprofile', 'r_organization_social', 'w_organization_social', 'rw_organization_admin'],
            default => []
        };
    }

    public function httpHeaders(): array
    {
        return [
            'X-Restli-Protocol-Version' => '2.0.0'
        ];
    }

    public static function externalPostUrl(AccountResource $accountResource): string
    {
        return "https://linkedin.com/feed/update/{$accountResource->pivot->provider_post_id}";
    }
}
