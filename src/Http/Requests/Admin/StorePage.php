<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use SaguiAi\MixpostAdapter\Models\Block;
use SaguiAi\MixpostAdapter\Models\Page;
use SaguiAi\MixpostAdapter\Rules\ResourceStatusRule;

class StorePage extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'meta_title' => ['sometimes', 'nullable', 'max:255'],
            'meta_description' => ['sometimes', 'nullable', 'max:1000'],
            'slug' => ['required', 'max:255', 'unique:mixpost_pages,slug'],
            'layout' => ['required', Rule::in(['default', 'medium', 'small'])],
            'status' => ['required', 'integer', new ResourceStatusRule()],
            'blocks' => ['array']
        ];
    }

    public function handle(): Page
    {
        $blocks = Block::whereIn('id', $this->input('blocks', []))->pluck('id');

        DB::transaction(function () use (&$page, $blocks) {
            $page = Page::create([
                'name' => $this->input('name'),
                'slug' => Str::slug($this->input('slug')),
                'meta_title' => $this->input('meta_title'),
                'meta_description' => $this->input('meta_description'),
                'layout' => $this->input('layout'),
                'status' => $this->input('status')
            ]);

            foreach ($this->input('blocks', []) as $sortOrder => $blockId) {
                if (in_array($blockId, $blocks->toArray())) {
                    $page->blocks()->attach($blockId, [
                        'sort_order' => $sortOrder
                    ]);
                }
            }
        });

        return $page;
    }
}
