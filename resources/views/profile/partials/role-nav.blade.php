@php
    $user = auth()->user();
    $role = $user?->role;

    $roles = [
        'customer' => ['label' => 'Customer',    'bar' => 'bg-blue-600',    'badge' => 'bg-blue-100 text-blue-800',       'hint' => 'Browse venues and book your event'],
        'owner'    => ['label' => 'Venue owner', 'bar' => 'bg-emerald-600', 'badge' => 'bg-emerald-100 text-emerald-800', 'hint' => 'Manage your venues and reservation requests'],
        'admin'    => ['label' => 'Admin',       'bar' => 'bg-purple-600',  'badge' => 'bg-purple-100 text-purple-800',   'hint' => 'Manage all venues and reservations'],
    ];
    $current = $roles[$role] ?? null;

    // Each link: [label, address, route names that make it "active"]
    $links = [['Venues', route('venues.index'), ['venues.index', 'venues.show', 'venues.edit']]];

    if ($role === 'customer') {
        array_unshift($links, ['Dashboard', route('dashboard'), ['dashboard']]);
        $links[] = ['My reservations', route('reservations.index'), ['reservations.*']];
    } elseif ($role === 'owner') {
        array_unshift($links, ['Dashboard', route('dashboard'), ['dashboard']]);
        $links[] = ['Reservation requests', route('reservations.index'), ['reservations.*']];
        $links[] = ['Owner area', route('owner.index'), ['owner.*']];
    } elseif ($role === 'admin') {
        array_unshift($links, ['Dashboard', route('dashboard'), ['dashboard']]);
        $links[] = ['All reservations', route('reservations.index'), ['reservations.*']];
        $links[] = ['Admin area', route('admin.index'), ['admin.*']];
    }

    // The one main button for each role: [label, address]
    $action = match ($role) {
        'customer'       => ['Reserve a venue', route('venues.index')],
        'owner', 'admin' => ['+ Add venue', route('venues.create')],
        default          => null,
    };
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    {{-- Colored bar: shows who you are viewing as (logged-in users only) --}}
    @if ($current)
        <div class="{{ $current['bar'] }} text-white text-xs">
            <div class="max-w-5xl mx-auto px-4 py-1.5 flex flex-wrap items-center gap-x-2">
                <span class="font-semibold uppercase tracking-wide">Viewing as {{ $current['label'] }}</span>
                <span class="hidden sm:inline opacity-90">&middot; {{ $current['hint'] }}</span>
            </div>
        </div>
    @endif

    <div class="max-w-5xl mx-auto px-4">
        <div class="flex h-14 items-center justify-between">
            <div class="flex items-center gap-6">
                <a href="{{ route('venues.index') }}" class="font-semibold">Calauan Venue Finder</a>

                <div class="hidden md:flex items-center gap-5 text-sm">
                    @foreach ($links as [$label, $url, $patterns])
                        <a href="{{ $url }}"
                           class="h-14 inline-flex items-center border-b-2 {{ request()->routeIs(...$patterns) ? 'border-gray-900 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Right side: who you are, then the buttons (all the same size) --}}
            <div class="hidden md:flex items-center gap-3 text-sm">
                @auth
                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $current['badge'] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $current['label'] ?? ucfirst($role) }}
                    </span>
                    <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:underline">{{ $user->name }}</a>

                    @if ($action)
                        <x-btn :href="$action[1]">{{ $action[0] }}</x-btn>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-btn variant="secondary">Log out</x-btn>
                    </form>
                @else
                    <x-btn :href="route('login')" variant="secondary">Log in</x-btn>
                    <x-btn :href="route('register')">Register</x-btn>
                @endauth
            </div>

            <button @click="open = ! open" class="md:hidden p-2 text-gray-600 text-xl" aria-label="Menu">&#9776;</button>
        </div>
    </div>

    {{-- Phone menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden border-t border-gray-200 px-4 py-3 space-y-2 text-sm">
        @foreach ($links as [$label, $url, $patterns])
            <a href="{{ $url }}" class="block py-1 {{ request()->routeIs(...$patterns) ? 'font-semibold text-gray-900' : 'text-gray-600' }}">{{ $label }}</a>
        @endforeach

        @auth
            <div class="pt-2 border-t border-gray-200 flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-xs font-medium {{ $current['badge'] ?? 'bg-gray-100 text-gray-700' }}">{{ $current['label'] ?? ucfirst($role) }}</span>
                <a href="{{ route('profile.edit') }}" class="text-gray-700 hover:underline">{{ $user->name }}</a>
            </div>

            @if ($action)
                <x-btn :href="$action[1]" class="w-full">{{ $action[0] }}</x-btn>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-btn variant="secondary" class="w-full">Log out</x-btn>
            </form>
        @else
            <x-btn :href="route('login')" variant="secondary" class="w-full">Log in</x-btn>
            <x-btn :href="route('register')" class="w-full">Register</x-btn>
        @endauth
    </div>
</nav>