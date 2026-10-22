<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 py-8 space-y-6">
        <div>
            <h1 class="text-2xl font-semibold">Welcome, {{ $user->name }}</h1>
            <p class="text-gray-600">You are logged in as <strong>{{ $user->roleLabel() }}</strong>.</p>
        </div>

        {{-- CUSTOMER --}}
        @if ($user->isCustomer())
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-stat-card label="Pending requests" :value="$stats['pending']" :href="route('reservations.index')" />
                <x-stat-card label="Confirmed" :value="$stats['confirmed']" :href="route('reservations.index')" />
                <x-stat-card label="All my requests" :value="$stats['total']" :href="route('reservations.index')" />
            </div>
        @endif

        {{-- OWNER --}}
        @if ($user->isOwner())
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-stat-card label="My venues" :value="$stats['venues']" />
                <x-stat-card label="Requests to review" :value="$stats['pending']" :href="route('reservations.index')" :highlight="$stats['pending'] > 0" />
                <x-stat-card label="Confirmed bookings" :value="$stats['confirmed']" :href="route('reservations.index')" />
            </div>

            <div class="bg-white rounded shadow overflow-x-auto">
                <h2 class="px-4 pt-4 font-semibold">My venues</h2>
                <table class="min-w-full text-sm mt-2">
                    <tbody>
                        @forelse ($myVenues as $venue)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $venue->name }}</td>
                                <td class="px-4 py-2 text-gray-500">{{ $venue->barangay->name }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex justify-end gap-2">
                                        <x-btn :href="route('venues.show', $venue)" variant="secondary">View</x-btn>
                                        <x-btn :href="route('venues.edit', $venue)">Edit</x-btn>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-4 text-gray-500">You have no venues yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        {{-- ADMIN --}}
        @if ($user->isAdmin())
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-stat-card label="Venues" :value="$stats['venues']" :href="route('venues.index')" />
                <x-stat-card label="Pending reservations" :value="$stats['pending']" :href="route('reservations.index')" :highlight="$stats['pending'] > 0" />
                <x-stat-card label="Users" :value="$stats['users']" />
            </div>
        @endif
    </div>
</x-app-layout>