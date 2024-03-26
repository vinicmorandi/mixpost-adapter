<?php

namespace SaguiAi\MixpostAdapter\Facades;

use Illuminate\Support\Facades\Facade;
use SaguiAi\MixpostAdapter\Configs\ThemeConfig;

/**
 * @method static string render()
 * @method static ThemeConfig config()
 * @method static array colors()
 * @method static string primaryColor(string $weight = '500')
 *
 * @see \SaguiAi\MixpostAdapter\Theme
 */
class Theme extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'MixpostTheme';
    }
}
