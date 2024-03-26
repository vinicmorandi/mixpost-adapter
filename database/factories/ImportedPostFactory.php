<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\ImportedPost;
use SaguiAi\MixpostAdapter\Models\Workspace;

class ImportedPostFactory extends Factory
{
    protected $model = ImportedPost::class;

    public function definition()
    {
        return [
            'workspace_id' => Workspace::factory(),
            'account_id' => Account::factory()->state([
                'provider' => 'twitter'
            ]),
            'provider_post_id' => Str::random(),
            'content' => ['text' => $this->faker->paragraph()],
            'metrics' => [
                'likes' => $this->faker->randomDigit(),
                'retweets' => $this->faker->randomDigit(),
                'impressions' => $this->faker->randomDigit()
            ],
            'created_at' => $this->faker->dateTimeBetween('-90 days')
        ];
    }
}
