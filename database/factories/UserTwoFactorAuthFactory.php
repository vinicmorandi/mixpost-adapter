<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Models\User;
use SaguiAi\MixpostAdapter\Models\UserTwoFactorAuth;

class UserTwoFactorAuthFactory extends Factory
{
    protected $model = UserTwoFactorAuth::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'secret_key' => Str::random(32),
            'recovery_codes' => [Str::random(32)],
            'confirmed_at' => $this->faker->dateTime(),
        ];
    }
}
