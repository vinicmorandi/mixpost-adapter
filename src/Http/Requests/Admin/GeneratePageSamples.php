<?php

namespace SaguiAi\MixpostAdapter\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Artisan;
use SaguiAi\MixpostAdapter\Commands\GeneratePageSamples as GeneratePageSamplesCommand;

class GeneratePageSamples extends FormRequest
{
    public function rules(): array
    {
        return [
            'brand_name' => ['required', 'max:255'],
            'logo_url' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'register_url' => ['sometimes', 'nullable', 'max:255'],
            'destroy' => ['required', 'boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'brand_name.required' => 'Brand name is required',
            'logo_url.required' => 'Logo is required',
            'email.required' => 'Email is required',
        ];
    }

    public function handle(): void
    {
        Artisan::call(GeneratePageSamplesCommand::class, [
            '--force' => true,
            '--destroy' => $this->input('destroy'),
            '--brand_name' => $this->input('brand_name'),
            '--logo_url' => $this->input('logo_url'),
            '--email' => $this->input('email'),
            '--register_url' => $this->input('register_url'),
        ]);
    }
}
