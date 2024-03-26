<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth overflow-x-hidden">
<head>
    <title>@hasSection('title')
            @yield('title')
        @else
            {{ config('app.name') }}
        @endif</title>

    @yield('meta')

    @include('mixpost::partial.head')
</head>
<body class="font-sans">
@yield('content')
</body>
</html>
