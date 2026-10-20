<x-layouts.public :title="$venue->name">
    <a href="{{ route('venues.index') }}" class="text-sm underline">&larr; All venues</a>

    <div class="bg-white rounded shadow p-6 mt-3">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-semibold">{{ $venue->name }}</h1>
                <p class="text-gray-600">{{ $venue->address ?: 'No address yet' }}, {{ $venue->barangay->name }}</p>
            </div>
            <span class="text-xs px-2 py-1 rounded {{ $venue->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ $venue->is_verified ? 'Verified' : 'Unverified' }}
            </span>
        </div>

        <dl class="grid grid-cols-3 gap-4 mt-6 text-sm">
            <div><dt class="text-gray-500">Capacity</dt><dd>{{ $venue->capacity }} guests</dd></div>
            <div><dt class="text-gray-500">Base price</dt><dd>₱{{ number_format($venue->base_price, 2) }}</dd></div>
            <div><dt class="text-gray-500">Down payment</dt><dd>₱{{ number_format($venue->down_payment, 2) }}</dd></div>
        </dl>

        <h2 class="font-semibold mt-6 mb-2">Amenities</h2>
        <div class="flex flex-wrap gap-2">
            @forelse ($venue->amenities as $amenity)
                <span class="bg-gray-100 rounded px-2 py-1 text-sm">{{ $amenity->name }}</span>
            @empty
                <span class="text-gray-500 text-sm">None listed.</span>
            @endforelse
        </div>

        @if ($venue->owners()->exists())
            @auth
                @if (auth()->user()->role === 'customer')
                    <a href="{{ route('reservations.create', $venue) }}" class="inline-block mt-6 bg-indigo-600 text-white px-4 py-2 rounded">Reserve this venue</a>
                @endif
            @else
                <p class="mt-6 text-sm"><a href="{{ route('login') }}" class="underline">Log in</a> to reserve this venue.</p>
            @endauth
        @else
            <p class="mt-6 text-sm text-gray-500">This venue is information-only. Online reservation is not available.</p>
        @endif

        @can('update', $venue)
            <div class="flex gap-3 mt-6">
                <a href="{{ route('venues.edit', $venue) }}" class="bg-indigo-600 text-white px-4 py-2 rounded">Edit</a>
                <form method="POST" action="{{ route('venues.destroy', $venue) }}" onsubmit="return confirm('Delete this venue?')">
                    @csrf
                    @method('DELETE')
                    <button class="bg-red-600 text-white px-4 py-2 rounded">Delete</button>
                </form>
            </div>
        @endcan
    </div>
</x-layouts.public>