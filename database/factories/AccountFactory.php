<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\Workspace;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition()
    {
        $providers = ['twitter', 'mastodon', 'facebook_page', 'facebook_group'];

        $name = $this->faker->name;

        return [
            'uuid' => $this->faker->uuid,
            'workspace_id' => Workspace::factory(),
            'name' => $name,
            'username' => Str::camel($this->faker->name),
            'provider' => $providers[rand(0, 3)],
            'provider_id' => Str::random(),
            'media' => ['disk' => 'public', 'path' => '/'],
            'data' => null,
            'authorized' => true,
            'access_token' => ['access_token' => Str::random()]
        ];
    }
}
