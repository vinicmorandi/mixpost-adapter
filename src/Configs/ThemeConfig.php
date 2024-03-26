<?php

namespace SaguiAi\MixpostAdapter\Configs;

use SaguiAi\MixpostAdapter\Abstracts\Config;

class ThemeConfig extends Config
{
    public function group(): string
    {
        return 'theme';
    }

    public function form(): array
    {
        return [
            'logo_url' => '',
            'favicon_url' => '',
            'favicon_chrome_small_url' => '',
            'favicon_chrome_medium_url' => '',
        ];
    }

    public function rules(): array
    {
        return [
            'logo_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'favicon_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'favicon_chrome_small_url' => ['sometimes', 'nullable', 'string', 'max:255'],
            'favicon_chrome_medium_url' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
