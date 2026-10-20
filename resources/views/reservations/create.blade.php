<x-layouts.public title="Reserve a venue">
    <a href="{{ route('venues.show', $venue) }}" class="text-sm underline">&larr; Back to {{ $venue->name }}</a>
    <h1 class="text-2xl font-semibold mt-3 mb-4">Reserve {{ $venue->name }}</h1>

    <div class="bg-white rounded shadow p-6">
        <dl class="grid grid-cols-3 gap-4 text-sm mb-6">
            <div><dt class="text-gray-500">Capacity</dt><dd>{{ $venue->capacity }} guests</dd></div>
            <div><dt class="text-gray-500">Base price</dt><dd>₱{{ number_format($venue->base_price, 2) }}</dd></div>
            <div><dt class="text-gray-500">Down payment</dt><dd>₱{{ number_format($venue->down_payment, 2) }}</dd></div>
        </dl>

        <form method="POST" action="{{ route('reservations.store', $venue) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium">Start date</label>
                    <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}" class="mt-1 w-full rounded border-gray-300">
                    @error('start_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium">End date</label>
                    <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}" class="mt-1 w-full rounded border-gray-300">
                    @error('end_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="guest_count" class="block text-sm font-medium">Number of guests</label>
                <input id="guest_count" name="guest_count" type="number" min="1" value="{{ old('guest_count') }}" class="mt-1 w-full rounded border-gray-300">
                @error('guest_count')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            @if ($venue->down_payment > 0)
                <div class="rounded bg-gray-50 p-4 text-sm">
                    <p class="font-medium">Pay the down payment by GCash</p>
                    <p>Send ₱{{ number_format($venue->down_payment, 2) }} to
                        <strong>{{ $venue->gcash_number ?: 'the owner (ask for the number)' }}</strong>,
                        then upload your receipt below. The owner checks it by hand.</p>
                </div>
            @endif

            <div>
                <label for="receipt" class="block text-sm font-medium">
                    Payment receipt {{ $venue->down_payment > 0 ? '' : '(optional)' }}
                </label>
                <input id="receipt" name="receipt" type="file" accept="image/*" class="mt-1 block w-full text-sm">
                @error('receipt')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3">
                <button class="bg-indigo-600 text-white px-4 py-2 rounded">Send request</button>
                <a href="{{ route('venues.show', $venue) }}" class="px-4 py-2 underline">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.public>