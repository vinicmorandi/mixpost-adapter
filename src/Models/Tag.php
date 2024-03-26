<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SaguiAi\MixpostAdapter\Concerns\Model\HasUuid;
use SaguiAi\MixpostAdapter\Concerns\OwnedByWorkspace;

class Tag extends Model
{
    use HasFactory;
    use HasUuid;
    use OwnedByWorkspace;

    public $table = 'mixpost_tags';

    protected $fillable = [
        'uuid',
        'name',
        'hex_color'
    ];
}
