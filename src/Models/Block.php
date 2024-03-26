<?php

namespace SaguiAi\MixpostAdapter\Models;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SaguiAi\MixpostAdapter\Enums\ResourceStatus;

class Block extends Model
{
    use HasFactory;

    public $table = 'mixpost_blocks';

    protected $fillable = [
        'name',
        'module',
        'content',
        'status'
    ];

    protected $casts = [
        'content' => 'array',
        'status' => ResourceStatus::class
    ];

    public function scopeEnabled(Builder $query): void
    {
        $query->where('status', ResourceStatus::ENABLED);
    }

    public function render(): string|Factory|View|Application
    {
        $modules = scandir(__DIR__ . '/../BlockModules');

        foreach ($modules as $module) {
            if (str_contains($module, $this->module)) {
                $module = 'SaguiAi\\MixpostAdapter\\BlockModules\\' . $this->module;

                return app($module)->render($this);
            }
        }

        return '';
    }
}
