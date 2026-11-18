<x-layouts.public title="Add a venue">
    <a href="{{ route('venues.index') }}" class="text-sm text-gray-600 hover:text-indigo-700">&larr; Back to venues</a>

    <h1 class="mt-3 text-2xl font-semibold">Add a venue</h1>
    <p class="mt-1 text-gray-600">Fill in the details. Customers will see this on the venue list.</p>

    <form method="POST" action="{{ route('venues.store') }}" class="mt-6 max-w-2xl space-y-5 rounded-2xl border border-gray-200 bg-white p-6">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Venue name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required
                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
            <input id="address" name="address" type="text" value="{{ old('address') }}" required
                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="barangay_id" class="block text-sm font-medium text-gray-700">Barangay</label>
            <select id="barangay_id" name="barangay_id" required
                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Choose a barangay</option>
                @foreach ($barangays as $barangay)
                    <option value="{{ $barangay->id }}" @selected(old('barangay_id') == $barangay->id)>{{ $barangay->name }}</option>
                @endforeach
            </select>
            @error('barangay_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-3">
            <div>
                <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity (guests)</label>
                <input id="capacity" name="capacity" type="number" min="1" value="{{ old('capacity') }}" required
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('capacity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="base_price" class="block text-sm font-medium text-gray-700">Base price (₱)</label>
                <input id="base_price" name="base_price" type="number" min="0" step="0.01" value="{{ old('base_price') }}" required
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('base_price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="down_payment" class="block text-sm font-medium text-gray-700">Down payment (₱)</label>
                <input id="down_payment" name="down_payment" type="number" min="0" step="0.01" value="{{ old('down_payment') }}" required
                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('down_payment') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="gcash_number" class="block text-sm font-medium text-gray-700">GCash number</label>
            <input id="gcash_number" name="gcash_number" type="text" value="{{ old('gcash_number') }}" required
                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('gcash_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <fieldset>
            <legend class="block text-sm font-medium text-gray-700">Amenities</legend>
            <div class="mt-2 grid gap-2 sm:grid-cols-2">
                @foreach ($amenities as $amenity)
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}"
                            @checked(in_array($amenity->id, old('amenities', [])))
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        {{ $amenity->name }}
                    </label>
                @endforeach
            </div>
            @error('amenities') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </fieldset>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                Save venue
            </button>
            <a href="{{ route('venues.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
        </div>
    </form>
</x-layouts.public>