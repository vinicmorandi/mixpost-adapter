<?php

namespace SaguiAi\MixpostAdapter\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Inertia\Middleware;
use SaguiAi\MixpostAdapter\Abstracts\User;
use SaguiAi\MixpostAdapter\Concerns\UsesAuth;
use SaguiAi\MixpostAdapter\Concerns\UsesUserResource;
use SaguiAi\MixpostAdapter\Facades\Settings;
use SaguiAi\MixpostAdapter\Facades\Theme;
use SaguiAi\MixpostAdapter\Mixpost;
use SaguiAi\MixpostAdapter\Util;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    use UsesAuth;
    use UsesUserResource;

    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'mixpost::layouts.app';

    /**
     * Determine the current asset version.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    public function version(Request $request)
    {
        if (file_exists($manifest = public_path('vendor/mixpost/manifest.json'))) {
            return md5_file($manifest);
        }

        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => $this->auth(),
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->filter(['mixpost.*'])->toArray(), [
                    'location' => $request->url(),
                    'workspace_id' => $this->getWorkspaceId($request)
                ]);
            },
            'is_admin_console' => Util::isAdminConsole($request),
            'flash' => function () use ($request) {
                return [
                    'success' => $request->hasSession() ? $request->session()->get('success') : null,
                    'warning' => $request->hasSession() ? $request->session()->get('warning') : null,
                    'error' => $request->hasSession() ? $request->session()->get('error') : null,
                    'info' => $request->hasSession() ? $request->session()->get('info') : null,
                ];
            },
            'app' => [
                'name' => Config::get('app.name')
            ],
            'mixpost' => [
                'docs_link' => 'https://docs.inovector.com',
                'mime_types' => Config::get('mixpost.mime_types'),
                'settings' => [
                    'locale' => Settings::get('locale'),
                    'timezone' => Settings::get('timezone'),
                    'time_format' => Settings::get('time_format'),
                    'week_starts_on' => Settings::get('week_starts_on'),
                ],
                'theme' => [
                    'logo' => Theme::config()->get('logo_url'),
                    'colors' => Theme::colors()
                ],
                'enterpriseConsole' => [
                    'url' => Mixpost::getEnterpriseConsoleUrl(),
                    'registration_url' => Mixpost::getRegistrationUrl(),
                    'create_workspace_url' => Mixpost::getCreateWorkspaceUrl(),
                    'has_workspace_urls' => Mixpost::hasWorkspaceUrls(),
                    'workspace_settings_url' => Mixpost::getWorkspaceSettingsUrl(),
                    'workspace_billing_url' => Mixpost::getWorkspaceBillingUrl(),
                    'workspace_upgrade_url' => Mixpost::getWorkspaceUpgradeUrl(),
                    'stop_impersonating_url' => Mixpost::getStopImpersonatingUrl(),
                    'multiple_workspace_enabled' => Mixpost::getMultipleWorkspaceEnabled(),
                ]
            ]
        ]);
    }

    protected function auth(): array
    {
        if (!self::getAuthGuard()->check()) {
            return [
                'user' => null,
                'workspaces' => [],
                'impersonating' => false,
            ];
        }

        $user = self::getAuthGuard()->user();

        // If `Auth Middleware` was not resolved first
        // return empty auth
        if (!$user instanceof User) {
            return [];
        }

        $userResourceClass = self::getUserResourceClass();

        return [
            'user' => new $userResourceClass($user->load(['admin', 'workspaces'])),
            'impersonating' => Mixpost::impersonating(),
        ];
    }

    protected function getWorkspaceId(Request $request)
    {
        // Exclude from Admin Console
        if (Util::isAdminConsole($request)) {
            return null;
        }

        return $request->route('workspace');
    }
}
