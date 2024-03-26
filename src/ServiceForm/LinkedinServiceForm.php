<?php

namespace SaguiAi\MixpostAdapter\ServiceForm;

use Illuminate\Validation\Rule;
use SaguiAi\MixpostAdapter\Abstracts\ServiceForm;

class LinkedinServiceForm extends ServiceForm
{
    public static array $configs = ['product'];

    static function form(): array
    {
        return [
            'client_id' => '',
            'client_secret' => '',
            'product' => 'sign_open_id_share'
        ];
    }

    public static function rules(): array
    {
        return [
            'client_id' => ['required'],
            'client_secret' => ['required'],
            'product' => ['required', Rule::in(['sign_share', 'sign_open_id_share', 'community_management'])],
        ];
    }

    public static function messages(): array
    {
        return [
            'client_id' => __('validation.required', ['attribute' => 'Client ID']),
            'client_secret' => __('validation.required', ['attribute' => 'Client Secret']),
            'product' => __('validation.in', ['attribute' => 'Product'])
        ];
    }
}
