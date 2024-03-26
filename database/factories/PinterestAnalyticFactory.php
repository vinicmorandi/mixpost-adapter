<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\PinterestAnalytic;
use SaguiAi\MixpostAdapter\Models\Workspace;

class PinterestAnalyticFactory extends Factory
{
    protected $model = PinterestAnalytic::class;

    public function definition()
    {
        return [
            'workspace_id' => Workspace::factory(),
            'account_id' => Account::factory()->state([
                'provider' => 'facebook_page'
            ]),
            'metrics' => [
                'likes' => $this->faker->randomDigit(),
                'retweets' => $this->faker->randomDigit(),
                'impressions' => $this->faker->randomDigit()
            ],
            'date' => $this->faker->dateTimeBetween('-90 days')
        ];
    }
}
