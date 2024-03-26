@php
    use SaguiAi\MixpostAdapter\Facades\Theme;

    $favicon = Theme::config()->get('favicon_url');
@endphp
<link rel="shortcut icon" href="{{ $favicon ? : asset('/vendor/mixpost/favicon/favicon.ico') }}">
<meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
<link rel="manifest" href="{{ route('mixpost.manifest') }}">
<meta name="theme-color" content="{{  Theme::primaryColor() }}">
