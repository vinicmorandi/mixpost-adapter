<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SaguiAi\MixpostAdapter\Concerns\OwnedByWorkspace;
use SaguiAi\MixpostAdapter\Enums\FacebookInsightType;

class FacebookInsight extends Model
{
    use HasFactory;
    use OwnedByWorkspace;

    public $table = 'mixpost_facebook_insights';

    protected $fillable = [
        'account_id',
        'type',
        'value',
        'date',
    ];

    protected $casts = [
        'type' => FacebookInsightType::class,
        'date' => 'date'
    ];

    public function scopeAccount($query, int $accountId)
    {
        $query->where('account_id', $accountId);
    }
}
