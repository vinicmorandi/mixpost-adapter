<?php

namespace SaguiAi\MixpostAdapter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SaguiAi\MixpostAdapter\Enums\ResourceStatus;
use SaguiAi\MixpostAdapter\Models\Page;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition()
    {
        $uuid = $this->faker->uuid();

        return [
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->sentence,
            'slug' => $uuid,
            'meta_title' => $this->faker->sentence,
            'meta_description' => $this->faker->sentence,
            'layout' => 'default',
            'status' => ResourceStatus::ENABLED
        ];
    }
}
