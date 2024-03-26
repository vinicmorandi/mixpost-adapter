<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Admin\Configs;

use Illuminate\Foundation\Http\FormRequest;
use SaguiAi\MixpostAdapter\Facades\Theme;

class SaveThemeConfig extends FormRequest
{
    public function rules(): array
    {
        return Theme::config()->rules();
    }

    public function messages(): array
    {
        return Theme::config()->messages();
    }

    public function handle(): void
    {
        Theme::config()->save();
    }
}
