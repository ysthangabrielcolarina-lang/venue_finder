<x-layouts.public title="Reservation">
    <a href="{{ route('reservations.index') }}" class="text-sm underline">&larr; All reservations</a>

    @error('status')
        <div class="mt-3 rounded bg-red-100 text-red-800 px-4 py-2">{{ $message }}</div>
    @enderror

    <div class="bg-white rounded shadow p-6 mt-3">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-semibold">{{ $reservation->venue->name }}</h1>
                <p class="text-gray-600">{{ $reservation->venue->barangay->name }}</p>
            </div>
            @include('reservations._status', ['status' => $reservation->status])
        </div>

        <dl class="grid grid-cols-2 gap-4 mt-6 text-sm">
            <div><dt class="text-gray-500">Customer</dt><dd>{{ $reservation->user->name }}</dd></div>
            <div><dt class="text-gray-500">Guests</dt><dd>{{ $reservation->guest_count }}</dd></div>
            <div><dt class="text-gray-500">Start date</dt><dd>{{ $reservation->start_date->format('F j, Y') }}</dd></div>
            <div><dt class="text-gray-500">End date</dt><dd>{{ $reservation->end_date->format('F j, Y') }}</dd></div>
            <div><dt class="text-gray-500">Down payment</dt><dd>₱{{ number_format($reservation->venue->down_payment, 2) }}</dd></div>
            @if ($reservation->status === 'pending' && $reservation->expires_at)
                <div><dt class="text-gray-500">Held until</dt><dd>{{ $reservation->expires_at->format('M j, Y g:i A') }}</dd></div>
            @endif
        </dl>

        @if ($reservation->receipt_path)
            <p class="mt-6 text-sm">
                <a href="{{ route('reservations.receipt', $reservation) }}" target="_blank" class="text-indigo-700 underline">View payment receipt</a>
            </p>
        @endif

        <div class="flex flex-wrap gap-3 mt-6">
            @can('manage', $reservation)
                @if ($reservation->status === 'pending')
                    <form method="POST" action="{{ route('reservations.confirm', $reservation) }}">
                        @csrf @method('PATCH')
                        <x-btn variant="success">Confirm</x-btn>
                    </form>
                    <form method="POST" action="{{ route('reservations.reject', $reservation) }}">
                        @csrf @method('PATCH')
                        <x-btn variant="danger">Reject</x-btn>
                    </form>
                @endif
            @endcan

            @can('update', $reservation)
                <x-btn :href="route('reservations.edit', $reservation)">Edit request</x-btn>
            @endcan

            @can('cancel', $reservation)
                <form method="POST" action="{{ route('reservations.cancel', $reservation) }}" onsubmit="return confirm('Cancel this reservation?')">
                    @csrf @method('PATCH')
                    <x-btn variant="secondary">Cancel reservation</x-btn>
                </form>
            @endcan

            @can('delete', $reservation)
                <form method="POST" action="{{ route('reservations.destroy', $reservation) }}" onsubmit="return confirm('Delete this reservation?')">
                    @csrf @method('DELETE')
                    <x-btn variant="danger">Delete</x-btn>
                </form>
            @endcan
        </div>
    </div>
</x-layouts.public>