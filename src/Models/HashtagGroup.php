<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SaguiAi\MixpostAdapter\Concerns\Model\HasUuid;
use SaguiAi\MixpostAdapter\Concerns\OwnedByWorkspace;

class HashtagGroup extends Model
{
    use HasFactory;
    use HasUuid;
    use OwnedByWorkspace;

    public $table = 'mixpost_hashtaggroups';

    protected $fillable = [
        'uuid',
        'name',
        'content'
    ];
}
