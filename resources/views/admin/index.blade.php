<x-layouts.public title="Admin area">
    <h1 class="text-2xl font-semibold mb-4">Admin area</h1>

    <h2 class="font-semibold mb-2">Users ({{ $users->count() }})</h2>
    <div class="bg-white rounded shadow overflow-x-auto mb-8">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Approved</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $account)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $account->name }}</td>
                        <td class="px-4 py-2 text-gray-600">{{ $account->email }}</td>
                        <td class="px-4 py-2">{{ $account->roleLabel() }}</td>
                        <td class="px-4 py-2">{{ $account->is_approved ? 'Yes' : 'No' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <h2 class="font-semibold mb-2">Venues and their owners ({{ $venues->count() }})</h2>
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-2">Venue</th>
                    <th class="px-4 py-2">Barangay</th>
                    <th class="px-4 py-2">Owners</th>
                    <th class="px-4 py-2 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($venues as $venue)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $venue->name }}</td>
                        <td class="px-4 py-2">{{ $venue->barangay->name }}</td>
                        <td class="px-4 py-2">{{ $venue->owners->pluck('name')->join(', ') ?: 'None' }}</td>
                        <td class="px-4 py-2">
                            <div class="flex justify-end gap-2">
                                <x-btn :href="route('venues.show', $venue)" variant="secondary">View</x-btn>
                                <x-btn :href="route('venues.edit', $venue)">Edit</x-btn>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No venues yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.public>