<?php

namespace SaguiAi\MixpostAdapter\Contracts;

interface ServiceForm
{
    public static function form(): array;

    public static function rules(): array;

    public static function messages(): array;
}
