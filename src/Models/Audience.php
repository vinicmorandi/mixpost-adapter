<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SaguiAi\MixpostAdapter\Concerns\OwnedByWorkspace;

class Audience extends Model
{
    use OwnedByWorkspace;
    use HasFactory;

    public $table = 'mixpost_audience';

    protected $fillable = [
        'account_id',
        'total',
        'date',
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public $timestamps = false;

    public function scopeAccount($query, int $accountId)
    {
        $query->where('account_id', $accountId);
    }
}
