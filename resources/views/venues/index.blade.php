<x-layouts.public title="Find a venue" full>
    {{-- Hero with the search card --}}
    <section class="bg-indigo-600">
        <div class="max-w-6xl mx-auto px-4 py-12 sm:py-16">
            <h1 class="text-3xl font-semibold text-white sm:text-4xl">Find the perfect venue in Calauan</h1>
            <p class="mt-3 max-w-2xl text-indigo-100">Compare venues by location, capacity, and budget, then send your request to the owner.</p>

            <form method="GET" action="{{ route('venues.index') }}" class="mt-8 rounded-2xl bg-white p-4 shadow-lg sm:p-5">
                <div class="grid items-end gap-4 sm:grid-cols-2 lg:grid-cols-12">
                    <div class="lg:col-span-3">
                        <label for="q" class="block text-xs font-medium text-gray-600">Search</label>
                        <input id="q" name="q" type="text" value="{{ $filters['q'] }}" placeholder="Venue name or address"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="lg:col-span-3">
                        <label for="barangay" class="block text-xs font-medium text-gray-600">Barangay</label>
                        <select id="barangay" name="barangay"
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All of Calauan</option>
                            @foreach ($barangays as $barangay)
                                <option value="{{ $barangay->id }}" @selected((string) $filters['barangay'] === (string) $barangay->id)>{{ $barangay->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="lg:col-span-2">
                        <label for="guests" class="block text-xs font-medium text-gray-600">Guests</label>
                        <input id="guests" name="guests" type="number" min="1" value="{{ $filters['guests'] }}" placeholder="How many?"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="lg:col-span-2">
                        <label for="max_price" class="block text-xs font-medium text-gray-600">Max budget (₱)</label>
                        <input id="max_price" name="max_price" type="number" min="0" value="{{ $filters['max_price'] }}" placeholder="Any"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="sm:col-span-2 lg:col-span-2">
                        <x-btn class="w-full !h-10">Search</x-btn>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between border-t border-gray-100 pt-4 text-sm">
                    <label class="flex items-center gap-2 text-gray-600">
                        Sort by
                        <select name="sort" onchange="this.form.submit()"
                                class="rounded-lg border-gray-300 py-1 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="name" @selected($sort === 'name')>Name</option>
                            <option value="price_low" @selected($sort === 'price_low')>Price: low to high</option>
                            <option value="price_high" @selected($sort === 'price_high')>Price: high to low</option>
                            <option value="capacity" @selected($sort === 'capacity')>Largest capacity</option>
                        </select>
                    </label>

                    @if (array_filter($filters))
                        <a href="{{ route('venues.index') }}" class="text-indigo-700 hover:underline">Clear filters</a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- Results --}}
    <section class="max-w-6xl mx-auto px-4 py-10">
        <h2 class="mb-6 text-xl font-semibold">{{ $venues->total() }} {{ Str::plural('venue', $venues->total()) }} in Calauan</h2>

        @if ($venues->count())
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($venues as $venue)
                    <article class="flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md">
                        <a href="{{ route('venues.show', $venue) }}" class="block">
                            <div class="relative flex h-40 items-center justify-center bg-indigo-50">
                                <span class="text-5xl font-semibold text-indigo-300">{{ Str::upper(Str::substr($venue->name, 0, 1)) }}</span>
                                <span class="absolute left-3 top-3 rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-700">{{ $venue->barangay->name }}</span>
                            </div>
                        </a>

                        <div class="flex flex-1 flex-col p-5">
                            <a href="{{ route('venues.show', $venue) }}" class="text-lg font-semibold leading-snug hover:text-indigo-700">{{ $venue->name }}</a>
                            <p class="mt-1 truncate text-sm text-gray-500">{{ $venue->address ?: 'Calauan, Laguna' }}</p>
                            <p class="mt-3 text-sm text-gray-600">Up to {{ $venue->capacity }} guests</p>

                            <div class="mt-4 flex items-end justify-between">
                                <div>
                                    <p class="text-xs text-gray-500">From</p>
                                    <p class="text-lg font-semibold">₱{{ number_format($venue->base_price) }}</p>
                                </div>
                                @if ($venue->down_payment > 0)
                                    <p class="text-right text-xs text-gray-500">Down payment<br>₱{{ number_format($venue->down_payment) }}</p>
                                @endif
                            </div>

                            <div class="mt-5 flex gap-2 border-t border-gray-100 pt-4">
                                <x-btn :href="route('venues.show', $venue)" variant="secondary" class="flex-1">Details</x-btn>

                                @unless (auth()->user()?->isStaff())
                                    @if ($venue->isReservable())
                                        @auth
                                            <x-btn :href="route('reservations.create', $venue)" class="flex-1">Reserve</x-btn>
                                        @else
                                            <x-btn :href="route('login')" class="flex-1">Log in to reserve</x-btn>
                                        @endauth
                                    @endif
                                @endunless
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">{{ $venues->links() }}</div>
        @else
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-12 text-center">
                <p class="text-lg font-semibold">No venues match your search</p>
                <p class="mt-1 text-sm text-gray-500">Try fewer filters or a larger budget.</p>
                <div class="mt-4">
                    <x-btn :href="route('venues.index')" variant="secondary">Clear filters</x-btn>
                </div>
            </div>
        @endif
    </section>
</x-layouts.public>