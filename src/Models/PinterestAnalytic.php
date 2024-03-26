<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SaguiAi\MixpostAdapter\Concerns\OwnedByWorkspace;

class PinterestAnalytic extends Model
{
    use HasFactory;
    use OwnedByWorkspace;

    public $table = 'mixpost_pinterest_analytics';

    protected $fillable = [
        'account_id',
        'provider_post_id',
        'metrics',
        'date',
    ];

    protected $casts = [
        'metrics' => 'array',
        'date' => 'date'
    ];

    public function scopeAccount($query, int $accountId)
    {
        $query->where('account_id', $accountId);
    }
}
