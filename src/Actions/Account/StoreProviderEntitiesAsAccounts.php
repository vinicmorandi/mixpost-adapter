<?php

namespace SaguiAi\MixpostAdapter\Actions\Account;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Facades\SocialProviderManager;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;
use InvalidArgumentException;

class StoreProviderEntitiesAsAccounts
{
    public function __invoke(string $provider, array $items)
    {
        $method = 'store' . Str::studly(Str::plural($provider));

        if (method_exists($this, $method)) {
            return $this->$method($items);
        }

        throw new InvalidArgumentException("Provider [$provider] not supported entities.");
    }

    private function storeFacebookPages(array $items): void
    {
        $provider = SocialProviderManager::connect('facebook_page');

        /**
         * Get entities with access token
         *
         * @var SocialProviderResponse $responseEntities
         */
        $responseEntities = $provider->getEntities(withAccessToken: true);

        $entities = Arr::where($responseEntities->context(), function ($entity) use ($items) {
            return in_array($entity['id'], $items);
        });

        foreach ($entities as $account) {
            (new UpdateOrCreateAccount())(
                providerName: 'facebook_page',
                account: $account,
                accessToken: array_merge($provider->getAccessToken(), ['page_access_token' => $account['access_token']['access_token']])
            );
        }
    }

    private function storeFacebookGroups(array $items): void
    {
        $provider = SocialProviderManager::connect('facebook_group');

        /**
         * Get entities with access token
         *
         * @var SocialProviderResponse $entities
         */
        $entities = $provider->getEntities();

        $entities = Arr::where($entities->context(), function ($entity) use ($items) {
            return in_array($entity['id'], $items);
        });

        $accessToken = $provider->getAccessToken();

        foreach ($entities as $account) {
            (new UpdateOrCreateAccount())(
                providerName: 'facebook_group',
                account: $account,
                accessToken: $accessToken
            );
        }
    }

    private function storeInstagrams(array $items): void
    {
        $provider = SocialProviderManager::connect('instagram');

        /**
         * Get entities with access token
         *
         * @var SocialProviderResponse $entities
         */
        $entities = $provider->getEntities();

        $entities = Arr::where($entities->context(), function ($entity) use ($items) {
            return in_array($entity['id'], $items);
        });

        $accessToken = $provider->getAccessToken();

        foreach ($entities as $account) {
            (new UpdateOrCreateAccount())(
                providerName: 'instagram',
                account: $account,
                accessToken: $accessToken
            );
        }
    }

    private function storeYoutubes(array $items): void
    {
        $provider = SocialProviderManager::connect('youtube');

        /**
         * Get entities with access token
         *
         * @var SocialProviderResponse $entities
         */
        $entities = $provider->getEntities();

        $entities = Arr::where($entities->context(), function ($entity) use ($items) {
            return in_array($entity['id'], $items);
        });

        $accessToken = $provider->getAccessToken();

        foreach ($entities as $account) {
            (new UpdateOrCreateAccount())(
                providerName: 'youtube',
                account: $account,
                accessToken: $accessToken
            );
        }
    }

    private function storeLinkedinPages(array $items): void
    {
        $provider = SocialProviderManager::connect('linkedin_page');

        /**
         * Get entities with access token
         *
         * @var SocialProviderResponse $entities
         */
        $entities = $provider->getEntities();

        $entities = Arr::where($entities->context(), function ($entity) use ($items) {
            return in_array($entity['id'], $items);
        });

        $accessToken = $provider->getAccessToken();

        foreach ($entities as $account) {
            (new UpdateOrCreateAccount())(
                providerName: 'linkedin_page',
                account: $account,
                accessToken: $accessToken
            );
        }
    }
}
