<x-layouts.public title="Reservations">
    <h1 class="text-2xl font-semibold mb-4">
        {{ auth()->user()->role === 'customer' ? 'My reservations' : 'Reservations' }}
    </h1>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-2">Venue</th>
                    @if (auth()->user()->role !== 'customer')
                        <th class="px-4 py-2">Customer</th>
                    @endif
                    <th class="px-4 py-2">Dates</th>
                    <th class="px-4 py-2">Guests</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reservations as $reservation)
                    <tr class="border-t">
                        <td class="px-4 py-2">
                            <a href="{{ route('reservations.show', $reservation) }}" class="text-indigo-700 underline">{{ $reservation->venue->name }}</a>
                        </td>
                        @if (auth()->user()->role !== 'customer')
                            <td class="px-4 py-2">{{ $reservation->user->name }}</td>
                        @endif
                        <td class="px-4 py-2">
                            {{ $reservation->start_date->format('M j, Y') }}
                            @unless ($reservation->start_date->equalTo($reservation->end_date))
                                to {{ $reservation->end_date->format('M j, Y') }}
                            @endunless
                        </td>
                        <td class="px-4 py-2">{{ $reservation->guest_count }}</td>
                        <td class="px-4 py-2">@include('reservations._status', ['status' => $reservation->status])</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">No reservations yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $reservations->links() }}</div>
</x-layouts.public>