<?php

namespace SaguiAi\MixpostAdapter;

use SaguiAi\MixpostAdapter\Abstracts\SocialProviderManager as SocialProviderManagerAbstract;
use SaguiAi\MixpostAdapter\Facades\Services;
use SaguiAi\MixpostAdapter\SocialProviders\Google\YoutubeProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Linkedin\LinkedinPageProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Linkedin\LinkedinProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Mastodon\MastodonProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\FacebookGroupProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\FacebookPageProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Meta\InstagramProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Pinterest\PinterestProvider;
use SaguiAi\MixpostAdapter\SocialProviders\TikTok\TikTokProvider;
use SaguiAi\MixpostAdapter\SocialProviders\Twitter\TwitterProvider;

class SocialProviderManager extends SocialProviderManagerAbstract
{
    protected function connectTwitterProvider()
    {
        $config = Services::get('twitter');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'twitter']);

        return $this->buildConnectionProvider(TwitterProvider::class, $config);
    }

    protected function connectFacebookPageProvider()
    {
        $config = Services::get('facebook');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'facebook_page']);

        return $this->buildConnectionProvider(FacebookPageProvider::class, $config);
    }

    protected function connectFacebookGroupProvider()
    {
        $config = Services::get('facebook');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'facebook_group']);

        return $this->buildConnectionProvider(FacebookGroupProvider::class, $config);
    }

    protected function connectInstagramProvider()
    {
        $config = Services::get('facebook');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'instagram']);

        return $this->buildConnectionProvider(InstagramProvider::class, $config);
    }

    protected function connectYoutubeProvider()
    {
        $config = Services::get('google');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'youtube']);

        return $this->buildConnectionProvider(YoutubeProvider::class, $config);
    }

    protected function connectPinterestProvider()
    {
        $config = Services::get('pinterest');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'pinterest']);
        $config['values'] = [
            'environment' => $config['environment'] ?? 'sandbox'
        ];

        return $this->buildConnectionProvider(PinterestProvider::class, $config);
    }

    protected function connectLinkedinProvider()
    {
        $config = Services::get('linkedin');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'linkedin']);

        return $this->buildConnectionProvider(LinkedinProvider::class, $config);
    }

    protected function connectLinkedinPageProvider()
    {
        $config = Services::get('linkedin');

        if (!LinkedinProvider::hasCommunityManagementProduct()) {
            abort(403);
        }

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'linkedin_page']);

        return $this->buildConnectionProvider(LinkedinPageProvider::class, $config);
    }

    protected function connectTiktokProvider()
    {
        $config = Services::get('tiktok');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'tiktok']);

        return $this->buildConnectionProvider(TikTokProvider::class, $config);
    }

    protected function connectMastodonProvider()
    {
        $request = $this->container->request;
        $sessionServerKey = "{$this->config->get('mixpost.cache_prefix')}.mastodon_server";

        if ($request->route() && $request->route()->getName() === 'mixpost.accounts.add') {
            $serverName = $this->container->request->input('server');
            $request->session()->put($sessionServerKey, $serverName); // We keep the server name in the session. We'll need it in the callback
        } else if ($request->route() && $request->route()->getName() === 'mixpost.callbackSocialProvider') {
            $serverName = $request->session()->get($sessionServerKey);
        } else {
            $serverName = $this->values['data']['server']; // Get the server value that have been set on SocialProviderManager::connect($provider, array $values = [])
        }

        $config = Services::get("mastodon.$serverName");

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'mastodon']);
        $config['values'] = [
            'data' => ['server' => $serverName]
        ];

        return $this->buildConnectionProvider(MastodonProvider::class, $config);
    }
}
