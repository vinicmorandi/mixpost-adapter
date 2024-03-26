<?php

namespace SaguiAi\MixpostAdapter\ServiceForm;

use Illuminate\Validation\Rule;
use SaguiAi\MixpostAdapter\Abstracts\ServiceForm;

class FacebookServiceForm extends ServiceForm
{
    public static function versions(): array
    {
        return ['v19.0', 'v18.0', 'v17.0', 'v16.0'];
    }

    static function form(): array
    {
        return [
            'client_id' => '',
            'client_secret' => '',
            'api_version' => current(self::versions())
        ];
    }

    public static function rules(): array
    {
        return [
            "client_id" => ['required'],
            "client_secret" => ['required'],
            "api_version" => ['required', Rule::in(self::versions())],
        ];
    }

    public static function messages(): array
    {
        return [
            'client_id' => __('validation.required', ['attribute' => 'App ID']),
            'client_secret' => __('validation.required', ['attribute' => 'APP Secret']),
            'api_version' => __('validation.required', ['attribute' => 'API Version']),
        ];
    }
}
