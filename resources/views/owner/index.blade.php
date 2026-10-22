<x-layouts.public title="Owner area">
    <h1 class="text-2xl font-semibold mb-4">Owner area</h1>

    <h2 class="font-semibold mb-2">My venues</h2>
    <div class="bg-white rounded shadow overflow-x-auto mb-8">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-2">Venue</th>
                    <th class="px-4 py-2">Barangay</th>
                    <th class="px-4 py-2">Pending</th>
                    <th class="px-4 py-2">Confirmed</th>
                    <th class="px-4 py-2 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($venues as $venue)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $venue->name }}</td>
                        <td class="px-4 py-2">{{ $venue->barangay->name }}</td>
                        <td class="px-4 py-2">{{ $venue->pending_count }}</td>
                        <td class="px-4 py-2">{{ $venue->confirmed_count }}</td>
                        <td class="px-4 py-2">
                            <div class="flex justify-end gap-2">
                                <x-btn :href="route('venues.show', $venue)" variant="secondary">View</x-btn>
                                <x-btn :href="route('venues.edit', $venue)">Edit</x-btn>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">You have no venues yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h2 class="font-semibold mb-2">Requests waiting for your decision</h2>
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-2">Venue</th>
                    <th class="px-4 py-2">Customer</th>
                    <th class="px-4 py-2">Dates</th>
                    <th class="px-4 py-2">Guests</th>
                    <th class="px-4 py-2">Held until</th>
                    <th class="px-4 py-2 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pending as $reservation)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $reservation->venue->name }}</td>
                        <td class="px-4 py-2">{{ $reservation->user->name }}</td>
                        <td class="px-4 py-2">
                            {{ $reservation->start_date->format('M j, Y') }}
                            @unless ($reservation->start_date->equalTo($reservation->end_date))
                                to {{ $reservation->end_date->format('M j, Y') }}
                            @endunless
                        </td>
                        <td class="px-4 py-2">{{ $reservation->guest_count }}</td>
                        <td class="px-4 py-2">{{ $reservation->expires_at->format('M j, g:i A') }}</td>
                        <td class="px-4 py-2">
                            <div class="flex justify-end">
                                <x-btn :href="route('reservations.show', $reservation)">Review</x-btn>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No requests are waiting.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.public>