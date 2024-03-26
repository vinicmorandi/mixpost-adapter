<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SaguiAi\MixpostAdapter\Casts\EncryptArrayObject;

class UserTwoFactorAuth extends Model
{
    use HasFactory;

    public $table = 'mixpost_user_two_factor_auth';

    protected $fillable = [
        'secret_key',
        'recovery_codes',
        'confirmed_at',
    ];

    protected $casts = [
        'secret_key' => 'encrypted',
        'recovery_codes' => EncryptArrayObject::class
    ];

    protected $hidden = [
        'secret_key',
        'recovery_codes',
    ];
}
