<?php

namespace SaguiAi\MixpostAdapter;

use Illuminate\Support\Arr;
use SaguiAi\MixpostAdapter\Configs\ThemeConfig;

class Theme
{
    public array $customColors = [];

    public function __construct(public readonly ThemeConfig $config)
    {
    }

    public function config(): ThemeConfig
    {
        return $this->config;
    }

    public function setCustomColors($value): void
    {
        $this->customColors = $value;
    }

    public function colors(): array
    {
        if (!empty($this->customColors)) {
            return $this->customColors;
        }

        return [
            'primary_colors' => [
                '50' => '#EDECF8',
                '100' => '#DCDAF1',
                '200' => '#B8B4E4',
                '500' => '#4F46BB',
                '700' => '#2F2970',
                '800' => '#1F1B4B',
                '900' => '#100E25',
            ],
            'primary_ring_focus' => 'rgba(184,180,228,0.5)',
            'primary_context' => '#ffffff',
            'alert' => '#1F1B4B',
            'alert_context' => '#e5e7eb',
        ];
    }

    public function primaryColor(string $weight = '500'): string
    {
        return Arr::get($this->colors(), "primary_colors.$weight");
    }

    public function render(): string
    {
        return view('mixpost::partial.theme', $this->colors())->render();
    }
}
