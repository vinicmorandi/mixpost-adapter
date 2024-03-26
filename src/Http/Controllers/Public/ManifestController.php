<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Public;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use SaguiAi\MixpostAdapter\Facades\Theme;

class ManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $faviconChromeSmall = Theme::config()->get('favicon_chrome_small_url');
        $faviconChromeMedium = Theme::config()->get('favicon_chrome_medium_url');

        $array = [
            'name' => Config::get('app.name'),
            'icons' => [
                [
                    'src' => $faviconChromeSmall ?: asset('/vendor/mixpost/favicon/favicon-192x192.png'),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                ],
                [
                    'src' => $faviconChromeMedium ?: asset('/vendor/mixpost/favicon/favicon-512x512.png'),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                ],
            ],
            'start_url' => route('mixpost.home'),
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => Theme::primaryColor()
        ];

        return response()->json($array);
    }
}
