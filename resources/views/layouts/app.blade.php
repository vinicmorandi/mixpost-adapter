@php
use SaguiAi\MixpostAdapter\Mixpost;
use SaguiAi\MixpostAdapter\Util;
@endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ Mixpost::getLocaleDirection() }}" class="scroll-smooth overflow-x-hidden">
<head>
    <title inertia>{{ config('app.name') }}</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="default_locale" content="{{ Util::config('default_locale') }}">
    @include('mixpost::partial.head')
    @if($bladePathScripts = Mixpost::getBladePathHeadScripts())
        @include($bladePathScripts)
    @endif
    @routes
    @inertiaHead
</head>
<body class="font-sans">
@if($bladePathScripts = Mixpost::getBladePathBodyScripts())
    @include($bladePathScripts)
@endif
@inertia
</body>
</html>
