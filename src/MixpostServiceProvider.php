<?php

namespace SaguiAi\MixpostAdapter;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Gate;
use SaguiAi\MixpostAdapter\Abstracts\User as UserAbstract;
use SaguiAi\MixpostAdapter\Actions\Common\MakeQueueWorkspaceAware;
use SaguiAi\MixpostAdapter\Commands\ClearServicesCache;
use SaguiAi\MixpostAdapter\Commands\ClearSettingsCache;
use SaguiAi\MixpostAdapter\Commands\ConvertLangJson;
use SaguiAi\MixpostAdapter\Commands\CreateAdmin;
use SaguiAi\MixpostAdapter\Commands\CreateMastodonApp;
use SaguiAi\MixpostAdapter\Commands\GeneratePageSamples;
use SaguiAi\MixpostAdapter\Commands\PublishAssetsCommand;
use SaguiAi\MixpostAdapter\Commands\RunWorkspaceCommand;
use SaguiAi\MixpostAdapter\Commands\Workspace\CheckAndRefreshAccountToken;
use SaguiAi\MixpostAdapter\Commands\Workspace\ImportAccountAudience;
use SaguiAi\MixpostAdapter\Commands\Workspace\ImportAccountData;
use SaguiAi\MixpostAdapter\Commands\Workspace\ProcessMetrics;
use SaguiAi\MixpostAdapter\Commands\Workspace\PruneTrashedPosts;
use SaguiAi\MixpostAdapter\Commands\Workspace\RunScheduledPosts;
use SaguiAi\MixpostAdapter\Concerns\UsesUserModel;
use SaguiAi\MixpostAdapter\Configs\ThemeConfig;
use SaguiAi\MixpostAdapter\Exceptions\MixpostExceptionHandler;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MixpostServiceProvider extends PackageServiceProvider
{
    use UsesUserModel;

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('mixpost')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('web')
            ->hasTranslations()
            ->hasMigrations([
                'create_mixpost_tables'
            ])
            ->hasCommands([
                PublishAssetsCommand::class,
                RunWorkspaceCommand::class,
                CreateMastodonApp::class,
                ClearSettingsCache::class,
                ClearServicesCache::class,
                RunScheduledPosts::class,
                ImportAccountAudience::class,
                ImportAccountData::class,
                CheckAndRefreshAccountToken::class,
                ProcessMetrics::class,
                PruneTrashedPosts::class,
                CreateAdmin::class,
                GeneratePageSamples::class,
                ConvertLangJson::class
            ])->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $this->writeSeparationLine($command);
                        $command->line('Mixpost Installation. Self-hosted social media management software.');
                        $command->line('Laravel version: ' . app()->version());
                        $command->line('PHP version: ' . trim(phpversion()));
                        $command->line(' ');
                        $command->line('Website: https://mixpost.app');
                        $this->writeSeparationLine($command);
                        $command->line('');

                        $command->comment('Publishing assets');
                        $command->call('mixpost:publish-assets');
                    })
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->endWith(function (InstallCommand $command) {
                        $hasUsers = self::getUserClass()::exists();

                        if (!$hasUsers) {
                            $appUrl = config('app.url');
                            $corePath = config('mixpost.core_path', 'mixpost');

                            $command->line("Visit the Mixpost dashboard at $appUrl/$corePath");
                        }

                        if ($hasUsers) {
                            $command->call('mixpost:create-admin');
                        }
                    });
            });
    }

    public function packageRegistered()
    {
        $this->app->singleton('MixpostWorkspaceManager', function () {
            return new WorkspaceManager();
        });

        $this->app->singleton('MixpostSocialProviderManager', function ($app) {
            return new SocialProviderManager($app);
        });

        $this->app->singleton('MixpostSettings', function ($app) {
            return new Settings($app);
        });

        $this->app->singleton('MixpostServices', function ($app) {
            return new Services($app);
        });

        $this->app->singleton('MixpostTheme', function ($app) {
            return new Theme(
                new ThemeConfig($app->request)
            );
        });
    }

    public function packageBooted()
    {
        $this->checkModelInstances();

        $this->configureQueue();

        $this->registerExceptionHandler();

        Gate::define('viewMixpost', function () {
            return true;
        });
    }

    protected function checkModelInstances(): void
    {
        $userModel = $this->app->make(config('mixpost.user_model'));

        if (!$userModel instanceof UserAbstract) {
            throw new \Exception('The user model must be an instance of SaguiAi\MixpostAdapter\Abstracts\User');
        }
    }

    protected function configureQueue(): void
    {
        app(MakeQueueWorkspaceAware::class)();
    }

    protected function registerExceptionHandler(): void
    {
        app()->bind(ExceptionHandler::class, MixpostExceptionHandler::class);
    }

    protected function writeSeparationLine(InstallCommand $command): void
    {
        $command->info('*---------------------------------------------------------------------------*');
    }
}
