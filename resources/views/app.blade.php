<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'SwiftNotify') }}</title>
        
        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='url(%23gradient)' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><defs><linearGradient id='gradient' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' style='stop-color:%234F46E5;stop-opacity:1' /><stop offset='100%' style='stop-color:%239333EA;stop-opacity:1' /></linearGradient></defs><path d='M13 2L3 14h9l-1 8 10-12h-9l1-8z'/></svg>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
        
        
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
