<x-layouts.public title="Venues">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Venues in Calauan</h1>
        @can('create', App\Models\Venue::class)
            <x-btn :href="route('venues.create')">+ Add venue</x-btn>
        @endcan
    </div>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Barangay</th>
                    <th class="px-4 py-2">Capacity</th>
                    <th class="px-4 py-2">Base price</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($venues as $venue)
                    <tr class="border-t">
                        <td class="px-4 py-2">
                            <a href="{{ route('venues.show', $venue) }}" class="text-indigo-700 underline">{{ $venue->name }}</a>
                        </td>
                        <td class="px-4 py-2">{{ $venue->barangay->name }}</td>
                        <td class="px-4 py-2">{{ $venue->capacity }}</td>
                        <td class="px-4 py-2">₱{{ number_format($venue->base_price, 2) }}</td>
                        <td class="px-4 py-2">{{ $venue->is_verified ? 'Verified' : 'Unverified' }}</td>
                        <td class="px-4 py-2 text-right">
                            @if (auth()->check() && in_array(auth()->user()->role, ['owner', 'admin'], true))
                                <x-btn :href="route('venues.show', $venue)" variant="secondary">View</x-btn>
                            @elseif ($venue->owners_count > 0)
                                @auth
                                    <x-btn :href="route('reservations.create', $venue)">Reserve</x-btn>
                                @else
                                    <x-btn :href="route('login')" variant="secondary">Log in to reserve</x-btn>
                                @endauth
                            @else
                                <span class="text-xs text-gray-500">Information only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No venues yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $venues->links() }}</div>
</x-layouts.public>