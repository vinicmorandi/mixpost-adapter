<?php

namespace SaguiAi\MixpostAdapter\ServiceForm;

use Illuminate\Validation\Rule;
use SaguiAi\MixpostAdapter\Abstracts\ServiceForm;

class PinterestServiceForm extends ServiceForm
{
    public static array $configs = ['environment'];

    static function form(): array
    {
        return [
            'client_id' => '',
            'client_secret' => '',
            'environment' => 'sandbox'
        ];
    }

    public static function rules(): array
    {
        return [
            "client_id" => ['required'],
            "client_secret" => ['required'],
            'environment' => ['required', Rule::in(['sandbox', 'production'])]
        ];
    }

    public static function messages(): array
    {
        return [
            'client_id' => __('validation.required', ['attribute' => 'App ID']),
            'client_secret' => __('validation.required', ['attribute' => 'APP Secret']),
            'environment' => __('validation.required', ['attribute' => 'Environment']),
        ];
    }
}
