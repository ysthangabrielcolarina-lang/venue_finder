<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Venue Finder' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 antialiased">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('venues.index') }}" class="font-semibold">Calauan Venue Finder</a>
                <a href="{{ route('venues.index') }}" class="underline">Venues</a>

                @auth
                    <a href="{{ route('dashboard') }}" class="underline">Dashboard</a>

                    @can('create', App\Models\Venue::class)
                        <a href="{{ route('venues.create') }}" class="underline">Add venue</a>
                    @endcan

                    @if (auth()->user()->role === 'owner')
                        <a href="{{ route('owner.index') }}" class="underline">Owner area</a>
                    @endif

                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.index') }}" class="underline">Admin area</a>
                    @endif
                @endauth
            </div>

            <div class="flex items-center gap-4 text-sm">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="underline">Log out ({{ auth()->user()->name }})</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="underline">Log in</a>
                    <a href="{{ route('register') }}" class="underline">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-6">
        @if (session('status'))
            <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2">{{ session('status') }}</div>
        @endif

        {{ $slot }}
    </main>
</body>
</html>