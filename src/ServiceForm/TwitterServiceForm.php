<?php

namespace SaguiAi\MixpostAdapter\ServiceForm;

use Illuminate\Validation\Rule;
use SaguiAi\MixpostAdapter\Abstracts\ServiceForm;

class TwitterServiceForm extends ServiceForm
{
    public static array $configs = ['tier'];

    static function form(): array
    {
        return [
            'client_id' => '',
            'client_secret' => '',
            'tier' => 'free'
        ];
    }

    public static function rules(): array
    {
        return [
            'client_id' => ['required'],
            'client_secret' => ['required'],
            'tier' => ['required', Rule::in(['legacy', 'free', 'basic'])]
        ];
    }

    public static function messages(): array
    {
        return [
            'client_id' => __('validation.required', ['attribute' => 'API Key']),
            'client_secret' => __('validation.required', ['attribute' => 'API Secret']),
            'tier' => __('validation.in', ['attribute' => 'Tier'])
        ];
    }
}
