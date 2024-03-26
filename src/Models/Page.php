<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use SaguiAi\MixpostAdapter\Concerns\Model\HasUuid;
use SaguiAi\MixpostAdapter\Enums\ResourceStatus;

class Page extends Model
{
    use HasFactory;
    use HasUuid;

    public $table = 'mixpost_pages';

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'meta_title',
        'meta_description',
        'layout',
        'status'
    ];

    protected $casts = [
        'status' => ResourceStatus::class
    ];

    public function blocks(): BelongsToMany
    {
        return $this->belongsToMany(Block::class, 'mixpost_page_block', 'page_id', 'block_id')
            ->orderByPivot('sort_order');
    }

    public function renderBlocks(): string
    {
        return $this->blocks()->enabled()->get()->map(function ($block) {
            return $block->render($this)->render();
        })->implode('');
    }
}
