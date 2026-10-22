<x-layouts.public title="Edit reservation">
    <a href="{{ route('reservations.show', $reservation) }}" class="text-sm underline">&larr; Back to the request</a>
    <h1 class="text-2xl font-semibold mt-3 mb-4">Edit request for {{ $reservation->venue->name }}</h1>

    <div class="bg-white rounded shadow p-6">
        <p class="text-sm text-gray-600 mb-4">
            Your dates stay held until {{ $reservation->expires_at->format('M j, Y g:i A') }}.
            Editing does not extend the hold.
        </p>

        <form method="POST" action="{{ route('reservations.update', $reservation) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium">Start date</label>
                    <input id="start_date" name="start_date" type="date"
                           value="{{ old('start_date', $reservation->start_date->format('Y-m-d')) }}"
                           class="mt-1 w-full rounded border-gray-300">
                    @error('start_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium">End date</label>
                    <input id="end_date" name="end_date" type="date"
                           value="{{ old('end_date', $reservation->end_date->format('Y-m-d')) }}"
                           class="mt-1 w-full rounded border-gray-300">
                    @error('end_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="guest_count" class="block text-sm font-medium">
                    Number of guests (up to {{ $reservation->venue->capacity }})
                </label>
                <input id="guest_count" name="guest_count" type="number" min="1"
                       value="{{ old('guest_count', $reservation->guest_count) }}"
                       class="mt-1 w-full rounded border-gray-300">
                @error('guest_count')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="receipt" class="block text-sm font-medium">
                    {{ $reservation->receipt_path ? 'Replace payment receipt (optional)' : 'Payment receipt' }}
                </label>
                @if ($reservation->receipt_path)
                    <p class="text-sm mt-1">
                        <a href="{{ route('reservations.receipt', $reservation) }}" target="_blank" class="text-indigo-700 underline">View current receipt</a>
                    </p>
                @endif
                <input id="receipt" name="receipt" type="file" accept="image/*" class="mt-1 block w-full text-sm">
                @error('receipt')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3">
                <x-btn>Save changes</x-btn>
                <x-btn :href="route('reservations.show', $reservation)" variant="secondary">Cancel</x-btn>
            </div>
        </form>
    </div>
</x-layouts.public>