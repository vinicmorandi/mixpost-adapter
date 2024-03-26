<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use SaguiAi\MixpostAdapter\Abstracts\User as UserAbstract;
use Illuminate\Notifications\Notifiable;

class User extends UserAbstract
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
