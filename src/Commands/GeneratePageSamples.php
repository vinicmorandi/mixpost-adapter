<?php

namespace SaguiAi\MixpostAdapter\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SaguiAi\MixpostAdapter\Enums\ResourceStatus;
use SaguiAi\MixpostAdapter\Models\Block;
use SaguiAi\MixpostAdapter\Models\Page;
use SaguiAi\MixpostAdapter\Util;

class GeneratePageSamples extends Command
{
    public $signature = 'mixpost:generate-page-samples {--force} {--destroy} {--brand_name=} {--email=mail@example.com} {--logo_url=#} {--register_url=}';

    public $description = 'Generate page samples (About, Terms of Use, Privacy Policy)';
    const PRIVACY_SLUG = 'privacy';
    const TERMS_SLUG = 'terms';

    public function handle(): int
    {
        $force = boolval($this->option('force'));

        if (!$force) {
            if (!$this->confirm('Are you sure you want to generate sample pages?')) {
                return self::FAILURE;
            }
        }

        if ($this->option('destroy')) {
            DB::transaction(function () {
                Page::query()->delete();
                Block::query()->delete();
            });

            $this->info('Current pages & blocks deleted successfully!');
        }

        $blocks = $this->storeBlocks();

        $this->storePage('Home', 'home', [$blocks['header'], $blocks['about_us'], $blocks['footer']]);
        $this->storePage('Terms Of Use', self::TERMS_SLUG, [$blocks['header'], $blocks['terms'], $blocks['footer']]);
        $this->storePage('Privacy Policy', self::PRIVACY_SLUG, [$blocks['header'], $blocks['privacy'], $blocks['footer']]);

        $this->info('Sample pages generated successfully!');

        return self::SUCCESS;
    }

    protected function storePage($name, $slug, $blocks): void
    {
        $exists = Page::where('slug', $slug)->exists();

        $slug = $exists ? $slug . '-' . Str::random() : $slug;

        DB::transaction(function () use ($name, $slug, $blocks) {
            $page = Page::create([
                'name' => $name,
                'slug' => $slug,
                'layout' => 'medium',
                'status' => ResourceStatus::ENABLED
            ]);

            foreach ($blocks as $index => $block) {
                $page->blocks()->attach($block, [
                    'sort_order' => $index
                ]);
            }
        });
    }

    protected function storeBlocks(): array
    {
        $header = Block::create([
            'name' => Block::where('name', 'Header')->exists() ? 'Header - ' . Str::random() : 'Header',
            'module' => 'Editor',
            'status' => ResourceStatus::ENABLED,
            'content' => [
                'body' => $this->getStub('header')
            ]
        ]);

        $footer = Block::create([
            'name' => Block::where('name', 'Footer')->exists() ? 'Footer - ' . Str::random() : 'Footer',
            'module' => 'Editor',
            'status' => ResourceStatus::ENABLED,
            'content' => [
                'body' => $this->getStub('footer')
            ]
        ]);

        $about = Block::create([
            'name' => Block::where('name', 'About')->exists() ? 'About - ' . Str::random() : 'About',
            'module' => 'Editor',
            'status' => ResourceStatus::ENABLED,
            'content' => [
                'body' => $this->getStub('about')
            ]
        ]);

        $privacyPolicy = Block::create([
            'name' => Block::where('name', 'Privacy Policy')->exists() ? 'Privacy Policy - ' . Str::random() : 'Privacy Policy',
            'module' => 'Editor',
            'status' => ResourceStatus::ENABLED,
            'content' => [
                'body' => $this->getStub('privacy')
            ]
        ]);

        $terms = Block::create([
            'name' => Block::where('name', 'Terms of Use')->exists() ? 'Terms of Use - ' . Str::random() : 'Terms of Use',
            'module' => 'Editor',
            'status' => ResourceStatus::ENABLED,
            'content' => [
                'body' => $this->getStub('terms')
            ]
        ]);

        return [
            'header' => $header->id,
            'footer' => $footer->id,
            'about_us' => $about->id,
            'privacy' => $privacyPolicy->id,
            'terms' => $terms->id
        ];
    }

    protected function getStub($name): array|bool|string
    {
        $content = file_get_contents(__DIR__ . "/../../stubs/Blocks/{$name}.stub");

        return str_replace(
            [
                '{{YourBrandName}}',
                '{{YourEmail}}',
                '{{LogoUrl}}',
                '{{RegisterUrl}}',
                '{{HomeUrl}}',
                '{{PrivacyUrl}}',
                '{{TermsUrl}}',
            ],
            [
                $this->option('brand_name') ?: Config::get('app.name'),
                $this->option('email'),
                $this->option('logo_url'),
                $this->option('register_url') ?: url(Util::corePath()),
                $this->getUrl(),
                $this->getUrl('/' . self::PRIVACY_SLUG),
                $this->getUrl('/' . self::TERMS_SLUG),
            ],
            $content
        );
    }

    protected function getUrl(string $path = ''): string
    {
        $prefix = Util::config('public_pages_prefix');

        if (!$prefix) {
            return url($path);
        }

        return url('/' . $prefix . $path);
    }
}
