<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in - Calauan Venue Finder</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 text-gray-900 antialiased">
<div class="min-h-screen grid md:grid-cols-2">

    {{-- Brand panel --}}
    <div class="hidden md:flex flex-col justify-between bg-indigo-600 text-white p-12">
        <a href="{{ route('venues.index') }}" class="text-lg font-semibold">Calauan Venue Finder</a>

        <div>
            <h1 class="text-4xl font-semibold leading-tight">Find the right venue for your event.</h1>
            <ul class="mt-8 space-y-4 text-indigo-100">
                <li>Browse venues across Calauan in one place</li>
                <li>Request your dates and pay the down payment by GCash</li>
                <li>Owners review and confirm each request</li>
            </ul>
        </div>

        <p class="text-sm text-indigo-200">Calauan, Laguna</p>
    </div>

    {{-- Form panel --}}
    <div class="flex items-center justify-center p-6 sm:p-12 bg-white">
        <div class="w-full max-w-sm">
            <a href="{{ route('venues.index') }}" class="md:hidden block font-semibold text-indigo-700 mb-8">Calauan Venue Finder</a>

            <h2 class="text-2xl font-semibold">Welcome back</h2>
            <p class="mt-1 text-sm text-gray-600">Log in to reserve a venue or manage your listings.</p>

            @if (session('status'))
                <div class="mt-4 rounded bg-green-100 text-green-800 px-4 py-2 text-sm">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-medium">Password</label>
                    <div class="relative mt-1">
                        <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password"
                               class="block w-full rounded-md border-gray-300 pr-16 focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="button" @click="show = ! show"
                                class="absolute inset-y-0 right-0 px-3 text-sm text-gray-500 hover:text-gray-800"
                                x-text="show ? 'Hide' : 'Show'">Show</button>
                    </div>
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-indigo-700 hover:underline">Forgot password?</a>
                    @endif
                </div>

                <x-btn class="w-full">Log in</x-btn>
            </form>

            <p class="mt-6 text-sm text-gray-600">
                New here? <a href="{{ route('register') }}" class="text-indigo-700 hover:underline">Create an account</a>
            </p>
            <p class="mt-2 text-sm">
                <a href="{{ route('venues.index') }}" class="text-gray-500 hover:underline">Browse venues without logging in</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>