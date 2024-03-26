<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use SaguiAi\MixpostAdapter\Models\Page;
use SaguiAi\MixpostAdapter\Rules\ResourceStatusRule;

class UpdatePage extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'meta_title' => ['sometimes', 'nullable', 'max:255'],
            'meta_description' => ['sometimes', 'nullable', 'max:1000'],
            'slug' => ['required', 'max:255', Rule::unique('mixpost_pages', 'slug')->ignore($this->route('page'), 'uuid')],
            'status' => ['required', 'integer', new ResourceStatusRule()],
            'layout' => ['required', Rule::in(['default', 'medium', 'small'])],
            'blocks' => ['array']
        ];
    }

    public function handle(): Page
    {
        $page = Page::firstOrFailByUuid($this->route('page'));

        DB::transaction(function () use ($page) {
            $page->update([
                'name' => $this->input('name'),
                'slug' => Str::slug($this->input('slug')),
                'meta_title' => $this->input('meta_title'),
                'meta_description' => $this->input('meta_description'),
                'layout' => $this->input('layout'),
                'status' => $this->input('status')
            ]);

            $page->blocks()->detach();

            foreach ($this->input('blocks', []) as $sortOrder => $blockId) {
                $page->blocks()->attach($blockId, [
                    'sort_order' => $sortOrder
                ]);
            }
        });

        return $page;
    }
}
