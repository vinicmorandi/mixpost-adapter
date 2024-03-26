<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SaguiAi\MixpostAdapter\Models\Account;
use SaguiAi\MixpostAdapter\Models\PostVersion;

class PostVersionFactory extends Factory
{
    protected $model = PostVersion::class;

    public function definition()
    {
        return [
            'account_id' => Account::factory(),
            'is_original' => 0,
            'content' => [
                [
                    "body" => "<div>👋 {$this->faker->paragraph}</div>
                               <div>{$this->faker->paragraph}</div>
                               <div>
                                <a target=\"_blank\" rel=\"noopener noreferrer nofollow\" href=\"https://mixpost.app\">https://mixpost.app</a>
                               </div>",
                    "media" => [3, 7, 5]
                ]
            ]
        ];
    }
}
