<?php

namespace SaguiAi\MixpostAdapter\Http\Controllers\Admin\Configs;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use SaguiAi\MixpostAdapter\Facades\Theme;
use SaguiAi\MixpostAdapter\Http\Requests\Admin\Configs\SaveThemeConfig;

class ThemeConfigController extends Controller
{
    public function form(): Response
    {
        return Inertia::render('Admin/Configs/ThemeConfig', [
            'configs' => Theme::config()->all()
        ]);
    }

    public function update(SaveThemeConfig $saveThemeConfig): \Symfony\Component\HttpFoundation\Response
    {
        $saveThemeConfig->handle();

        return Inertia::location(route('mixpost.configs.theme.form'));
    }
}
