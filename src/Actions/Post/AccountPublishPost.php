<?php

namespace SaguiAi\MixpostAdapter\Actions\Post;

use SaguiAi\MixpostAdapter\Concerns\UsesSocialProviderManager;
use SaguiAi\MixpostAdapter\Enums\SocialProviderResponseStatus;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\Post;
use SaguiAi\MixpostAdapter\Support\PostContentParser;
use SaguiAi\MixpostAdapter\Support\SocialProviderResponse;

class AccountPublishPost
{
    use UsesSocialProviderManager;

    public function __invoke(Account $account, Post $post): SocialProviderResponse
    {
        $parser = new PostContentParser($account, $post);

        $content = $parser->getVersionContent();

        if (empty($content)) {
            $errors = ['This account version has no content.'];

            $post->insertProviderAccountErrors($account, $errors);

            return new SocialProviderResponse(SocialProviderResponseStatus::ERROR, $errors);
        }

        $response = $this->connectProvider($account)->publishPost(
            text: $parser->formatBody($content[0]['body']),
            media: $parser->formatMedia($content[0]['media']),
            params: $parser->getVersionOptions()
        );

        if ($response->hasError()) {
            $post->insertErrors($account, $response->context());

            return $response;
        }

        $post->insertProviderData($account, $response);

        return $response;
    }
}
