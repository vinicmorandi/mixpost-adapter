<?php

namespace SaguiAi\MixpostAdapter\SocialProviders\Meta\Concerns;

trait ManagesFacebookOAuth
{
    public function getAuthUrl(): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl,
            'scope' => $this->scope,
            'response_type' => 'code',
            'state' => $this->values['state']
        ];

        $url = 'https://www.facebook.com/' . $this->apiVersion . '/dialog/oauth';

        return $this->buildUrlFromBase($url, $params);
    }
}
