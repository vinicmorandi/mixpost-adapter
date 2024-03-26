<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PostVersion extends Model
{
    use HasFactory;

    public $table = 'mixpost_post_versions';

    public $timestamps = false;

    protected $fillable = [
        'account_id',
        'is_original',
        'content',
        'options'
    ];

    protected $casts = [
        'is_original' => 'boolean',
        'content' => 'array',
        'options' => 'array'
    ];

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'mixpost_post_version_media', 'version_id', 'media_id');
    }
}
