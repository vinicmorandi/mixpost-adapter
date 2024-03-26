<?php

namespace SaguiAi\MixpostAdapter\ServiceForm;

use SaguiAi\MixpostAdapter\Abstracts\ServiceForm;

class TenorServiceForm extends ServiceForm
{
    static function form(): array
    {
        return [
            'client_id' => ''
        ];
    }

    public static function rules(): array
    {
        return [
            "client_id" => ['required']
        ];
    }

    public static function messages(): array
    {
        return [
            'client_id' => __('validation.required', ['attribute' => 'API Key']),
        ];
    }
}
