<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    public $table = 'mixpost_configs';

    protected $fillable = [
        'group',
        'name',
        'payload'
    ];

    protected $casts = [
        'payload' => 'array'
    ];

    public $timestamps = false;

    public static function get(string $property, mixed $default = null)
    {
        [$group, $name] = explode('.', $property);

        $config = self::query()
            ->where('group', $group)
            ->where('name', $name)
            ->first('payload');

        if (!$config) {
            return $default;
        }

        return $config->payload;
    }
}
