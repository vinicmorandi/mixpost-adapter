<?php

namespace SaguiAi\MixpostAdapter;

use Closure;

class Mixpost
{
    public static string $localeDirection = 'ltr';
    public static ?Closure $reportCallback = null;
    protected static string $enterpriseConsoleUrl = '';
    protected static string $registrationUrl = '';
    protected static string $createWorkspaceUrl = '';
    protected static string $workspaceSettingsUrl = '';
    protected static string $workspaceBillingUrl = '';
    protected static string $workspaceUpgradeUrl = '';
    protected static string $stopImpersonatingUrl = '';
    protected static bool $impersonating = false;
    protected static bool $multipleWorkspaceEnabled = false;
    protected static array $workspaceMiddlewares = [];
    protected static array $globalMiddlewares = [];
    protected static mixed $customWorkspaceModel = null;
    protected static mixed $customUserResource = null;
    protected static string $bladePathHeadScripts = '';
    protected static string $bladePathBodyScripts = '';
    protected static string $enterpriseVersion = '';

    public static function report(Closure $callback): static
    {
        static::$reportCallback = $callback;

        return new static();
    }

    public static function enterpriseConsoleUrl(string $url): void
    {
        self::$enterpriseConsoleUrl = $url;
    }

    public static function registrationUrl(string $url): void
    {
        self::$registrationUrl = $url;
    }

    public static function createWorkspaceUrl(string $url): string
    {
        return self::$createWorkspaceUrl = $url;
    }

    public static function workspaceSettingsUrl(string $url): string
    {
        return self::$workspaceSettingsUrl = $url;
    }

    public static function workspaceBillingUrl(string $url): string
    {
        return self::$workspaceBillingUrl = $url;
    }

    public static function workspaceUpgradeUrl(string $url): string
    {
        return self::$workspaceUpgradeUrl = $url;
    }

    public static function stopImpersonatingUrl(string $url): void
    {
        self::$stopImpersonatingUrl = $url;
    }

    public static function setImpersonating(bool $value): void
    {
        self::$impersonating = $value;
    }

    public static function globalMiddlewares(array $middlewares): array
    {
        return self::$globalMiddlewares = $middlewares;
    }

    public static function multipleWorkspaceEnabled(bool $value): void
    {
        self::$multipleWorkspaceEnabled = $value;
    }

    public static function workspaceMiddlewares(array $middlewares): array
    {
        return self::$workspaceMiddlewares = $middlewares;
    }

    public static function customWorkspaceModel(string $model): void
    {
        self::$customWorkspaceModel = $model;
    }

    public static function customUserResource(string $resource): void
    {
        self::$customUserResource = $resource;
    }

    public static function bladePathHeadScripts(string $path): void
    {
        self::$bladePathHeadScripts = $path;
    }

    public static function bladePathBodyScripts(string $path): void
    {
        self::$bladePathBodyScripts = $path;
    }

    public static function enterpriseVersion(string $version): void
    {
        self::$enterpriseVersion = $version;
    }

    public static function setLocaleDirection(string $direction): void
    {
        self::$localeDirection = $direction;
    }

    public static function getEnterpriseConsoleUrl(): string
    {
        return self::$enterpriseConsoleUrl;
    }

    public static function getRegistrationUrl(): string
    {
        return self::$registrationUrl;
    }

    public static function getCreateWorkspaceUrl(): string
    {
        return self::$createWorkspaceUrl;
    }

    public static function getWorkspaceSettingsUrl(): string
    {
        return self::$workspaceSettingsUrl;
    }

    public static function getWorkspaceBillingUrl(): string
    {
        return self::$workspaceBillingUrl;
    }

    public static function getWorkspaceUpgradeUrl(): string
    {
        return self::$workspaceUpgradeUrl;
    }

    public static function getStopImpersonatingUrl(): string
    {
        return self::$stopImpersonatingUrl;
    }

    public static function impersonating(): bool
    {
        return self::$impersonating;
    }

    public static function getMultipleWorkspaceEnabled(): bool
    {
        return self::$multipleWorkspaceEnabled;
    }

    public static function getGlobalMiddlewares(): array
    {
        return self::$globalMiddlewares;
    }

    public static function getWorkspaceMiddlewares(): array
    {
        return self::$workspaceMiddlewares;
    }

    public static function getCustomWorkspaceModel(): mixed
    {
        return self::$customWorkspaceModel;
    }

    public static function getCustomUserResource(): mixed
    {
        return self::$customUserResource;
    }

    public static function getBladePathHeadScripts(): string
    {
        return self::$bladePathHeadScripts;
    }

    public static function getBladePathBodyScripts(): string
    {
        return self::$bladePathBodyScripts;
    }

    public static function getLocaleDirection(): string
    {
        return self::$localeDirection;
    }

    public static function getEnterpriseVersion(): string
    {
        return self::$enterpriseVersion;
    }

    public static function hasWorkspaceUrls(): bool
    {
        return self::$workspaceSettingsUrl ||
            self::$workspaceBillingUrl ||
            self::$workspaceUpgradeUrl;
    }
}
