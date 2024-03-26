<?php

namespace SaguiAi\MixpostAdapter\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Facades\Settings;
use SaguiAi\MixpostAdapter\Mixpost;
use SaguiAi\MixpostAdapter\Util;

class Localization
{
    use UsesAuth;

    const DEFAULT_DIRECTION = 'ltr';

    public function handle(Request $request, Closure $next)
    {
        // Get user or default locale
        $locale = self::getAuthGuard()->check() ? Settings::get('locale') : Util::config('default_locale');

        // Fallback to default locale if logged in and selected user locale is not supported
        if (self::getAuthGuard()->check() && !$this->isLocaleSupported($locale)) {
            $locale = Util::config('default_locale');

            Mixpost::setLocaleDirection($this->getLocaleDirection($locale));
        }

        if ($locale !== App::getLocale()) {
            App::setLocale($locale);

            Mixpost::setLocaleDirection($this->getLocaleDirection($locale));
        }

        return $next($request);
    }

    protected function isLocaleSupported(string $locale): bool
    {
        $locales = Util::config('locales');

        return collect($locales)->contains(function ($value) use ($locale) {
            return $value['long'] === $locale;
        });
    }

    protected function getLocaleDirection(string $locale)
    {
        $locales = Util::config('locales');

        return Arr::get(collect($locales)->firstWhere('long', $locale), 'direction', self::DEFAULT_DIRECTION);
    }
}
