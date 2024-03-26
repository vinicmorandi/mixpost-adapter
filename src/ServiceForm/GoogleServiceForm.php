<?php

namespace SaguiAi\MixpostAdapter\ServiceForm;

use SaguiAi\MixpostAdapter\Abstracts\ServiceForm;

class GoogleServiceForm extends ServiceForm
{
    static function form(): array
    {
        return [
            'client_id' => '',
            'client_secret' => ''
        ];
    }

    public static function rules(): array
    {
        return [
            "client_id" => ['required'],
            "client_secret" => ['required'],
        ];
    }

    public static function messages(): array
    {
        return [
            'client_id' => __('validation.required', ['attribute' => 'App ID']),
            'client_secret' => __('validation.required', ['attribute' => 'APP Secret']),
        ];
    }
}
