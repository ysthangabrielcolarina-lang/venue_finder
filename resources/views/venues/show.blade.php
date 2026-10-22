<x-layouts.public :title="$venue->name">
    <a href="{{ route('venues.index') }}" class="text-sm text-gray-600 hover:text-indigo-700">&larr; All venues</a>

    <div class="mt-4 grid gap-8 lg:grid-cols-3">
        <div class="space-y-8 lg:col-span-2">
            <div class="flex h-56 items-center justify-center rounded-2xl bg-indigo-50 sm:h-72">
                <span class="text-7xl font-semibold text-indigo-300">{{ Str::upper(Str::substr($venue->name, 0, 1)) }}</span>
            </div>

            <div>
                <h1 class="text-3xl font-semibold">{{ $venue->name }}</h1>
                <p class="mt-1 text-gray-600">{{ $venue->address ?: 'No address yet' }} &middot; {{ $venue->barangay->name }}</p>
            </div>

            <dl class="grid grid-cols-3 gap-4 rounded-2xl border border-gray-200 bg-white p-5 text-sm">
                <div>
                    <dt class="text-gray-500">Capacity</dt>
                    <dd class="mt-1 text-lg font-semibold">{{ $venue->capacity }} guests</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Base price</dt>
                    <dd class="mt-1 text-lg font-semibold">₱{{ number_format($venue->base_price) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Down payment</dt>
                    <dd class="mt-1 text-lg font-semibold">₱{{ number_format($venue->down_payment) }}</dd>
                </div>
            </dl>

            <section>
                <h2 class="text-lg font-semibold">Amenities</h2>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse ($venue->amenities as $amenity)
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-sm text-gray-700">{{ $amenity->name }}</span>
                    @empty
                        <span class="text-sm text-gray-500">None listed.</span>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="lg:col-span-1">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:sticky lg:top-44">
                <p class="text-sm text-gray-500">From</p>
                <p class="text-3xl font-semibold">₱{{ number_format($venue->base_price) }}</p>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $venue->down_payment > 0 ? '₱'.number_format($venue->down_payment).' down payment to hold your dates' : 'No down payment needed' }}
                </p>

                <div class="mt-6 space-y-3">
                    @if ($venue->isReservable())
                        @auth
                            @if (auth()->user()->isCustomer())
                                <x-btn :href="route('reservations.create', $venue)" class="w-full">Reserve this venue</x-btn>
                                <p class="text-center text-xs text-gray-500">Your dates are held for 24 hours while the owner reviews your request.</p>
                            @endif
                        @else
                            <x-btn :href="route('login')" class="w-full">Log in to reserve</x-btn>
                        @endauth
                    @else
                        <p class="text-sm text-gray-500">Information only. Online reservation is not available.</p>
                    @endif

                    @can('update', $venue)
                        <div class="flex gap-2 border-t border-gray-100 pt-4">
                            <x-btn :href="route('venues.edit', $venue)" class="flex-1">Edit</x-btn>
                            <form method="POST" action="{{ route('venues.destroy', $venue) }}" onsubmit="return confirm('Delete this venue?')" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <x-btn variant="danger" class="w-full">Delete</x-btn>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </aside>
    </div>
</x-layouts.public>