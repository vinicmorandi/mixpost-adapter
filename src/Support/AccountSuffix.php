<?php

namespace SaguiAi\MixpostAdapter\Support;

use Illuminate\Support\Arr;

class AccountSuffix
{
    public static function key(): string
    {
        return 'suffix';
    }

    public static function schema(string $value, bool $edited = false): array
    {
        return [
            self::key() => [
                'value' => $value,
                'edited' => $edited,
            ]
        ];
    }

    public static function getValue(array $schema): string
    {
        return strval(Arr::get($schema, 'suffix.value', ''));
    }

    public static function isEdited(array $schema): bool
    {
        return boolval(Arr::get($schema, 'suffix.edited', false));
    }
}
