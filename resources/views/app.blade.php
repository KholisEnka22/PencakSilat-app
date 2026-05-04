<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('img/abitour.jpg') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script>
        const theme = localStorage.getItem('theme');

        if (
            theme === 'dark' ||
            (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)
        ) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @routes
    @viteReactRefresh
    @vite(['resources/js/app.tsx', 'resources/css/app.css'])
    @inertiaHead
</head>

<body class="min-h-screen bg-background font-sans text-foreground antialiased">
    @inertia
</body>

</html>
