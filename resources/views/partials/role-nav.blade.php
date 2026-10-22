<nav class="sticky top-0 z-40 bg-white border-b border-gray-200">
    {{-- Colored bar: shows who you are viewing as --}}
    @if ($current)
        <div class="{{ $current['bar'] }} text-white text-xs">
            <div class="max-w-6xl mx-auto px-4 py-1.5 font-semibold uppercase tracking-wide">
                Viewing as {{ $current['label'] }}
            </div>
        </div>
    @endif

    {{-- Brand and account --}}
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-sm font-semibold text-white">VF</span>
            <span class="hidden font-semibold sm:inline">Calauan Venue Finder</span>
        </a>

        <div class="flex items-center gap-3 text-sm">
            @if ($user)
                <a href="{{ route('profile.edit') }}" class="hidden text-gray-700 hover:text-indigo-700 sm:inline">{{ $user->name }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-btn variant="secondary">Log out</x-btn>
                </form>
            @else
                <x-btn :href="route('login')" variant="secondary">Log in</x-btn>
                <x-btn :href="route('register')">Register</x-btn>
            @endif
        </div>
    </div>

    {{-- Button row: the page you are on is filled in --}}
    @if ($buttons)
        <div class="border-t border-gray-100 bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 py-2 flex flex-wrap items-center gap-2">
                @foreach ($buttons as $button)
                    <x-btn :href="$button['url']" :variant="$button['active'] ? 'primary' : 'secondary'">{{ $button['label'] }}</x-btn>
                @endforeach

                @if ($action)
                    <x-btn :href="$action['url']" variant="success" class="sm:ml-auto">{{ $action['label'] }}</x-btn>
                @endif
            </div>
        </div>
    @endif
</nav>