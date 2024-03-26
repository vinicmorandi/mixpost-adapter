<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SaguiAi\MixpostAdapter\Models\Service;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition()
    {
        return [
            'name' => $this->faker->domainName,
            'credentials' => ['client_id' => $this->faker->randomDigit(), 'client_secret' => $this->faker->randomDigit()]
        ];
    }
}
