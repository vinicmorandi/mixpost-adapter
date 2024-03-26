<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;

class Admin extends Model
{
    use HasFactory;
    use UsesUserModel;

    public $table = 'mixpost_admins';

    protected $fillable = [
        'user_id'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(self::getUserClass(), 'user_id');
    }
}
