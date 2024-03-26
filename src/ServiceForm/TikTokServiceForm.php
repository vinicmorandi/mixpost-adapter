<?php

namespace SaguiAi\MixpostAdapter\ServiceForm;

use SaguiAi\MixpostAdapter\Abstracts\ServiceForm;

class TikTokServiceForm extends ServiceForm
{
    public static array $configs = ['share_type'];

    public static function form(): array
    {
        return [
            'client_id' => '',
            'client_secret' => '',
            'share_type' => 'inbox',
        ];
    }

    public static function rules(): array
    {
        return [
            'client_id' => ['required'],
            'client_secret' => ['required'],
            'share_type' => ['required'],
        ];
    }

    public static function messages(): array
    {
        return [
            'client_id' => __('validation.required', ['attribute' => 'Client Key']),
            'client_secret' => __('validation.required', ['attribute' => 'Client Secret']),
            'share_type' => __('validation.required', ['attribute' => 'Share type']),
        ];
    }
}
