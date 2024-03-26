<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SaguiAi\MixpostAdapter\Casts\PostingScheduleTimesCast;
use SaguiAi\MixpostAdapter\Concerns\OwnedByWorkspace;

class PostingSchedule extends Model
{
    use HasFactory;
    use OwnedByWorkspace;

    public $table = 'mixpost_posting_schedule';

    protected $fillable = [
        'uuid',
        'times',
    ];

    protected $casts = [
        'times' => PostingScheduleTimesCast::class
    ];
}
