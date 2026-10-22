<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-gray-100 text-gray-900 antialiased">
    @include('partials.role-nav')

    @isset($header)
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-5xl mx-auto px-4 py-4">{{ $header }}</div>
        </header>
    @endisset

    <main>{{ $slot }}</main>
</body>
</html>