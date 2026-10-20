@php($venue = $venue ?? null)
@php($selected = old('amenities', $venue ? $venue->amenities->pluck('id')->all() : []))

<div class="space-y-4">
    <div>
        <label for="name" class="block text-sm font-medium">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $venue?->name) }}" class="mt-1 w-full rounded border-gray-300">
        @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="barangay_id" class="block text-sm font-medium">Barangay</label>
        <select id="barangay_id" name="barangay_id" class="mt-1 w-full rounded border-gray-300">
            <option value="">Select a barangay</option>
            @foreach ($barangays as $barangay)
                <option value="{{ $barangay->id }}" @selected(old('barangay_id', $venue?->barangay_id) == $barangay->id)>
                    {{ $barangay->name }}
                </option>
            @endforeach
        </select>
        @error('barangay_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="address" class="block text-sm font-medium">Address</label>
        <input id="address" name="address" type="text" value="{{ old('address', $venue?->address) }}" class="mt-1 w-full rounded border-gray-300">
        @error('address')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label for="capacity" class="block text-sm font-medium">Capacity (guests)</label>
            <input id="capacity" name="capacity" type="number" min="1" value="{{ old('capacity', $venue?->capacity) }}" class="mt-1 w-full rounded border-gray-300">
            @error('capacity')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="base_price" class="block text-sm font-medium">Base price (₱)</label>
            <input id="base_price" name="base_price" type="number" step="0.01" min="0" value="{{ old('base_price', $venue?->base_price) }}" class="mt-1 w-full rounded border-gray-300">
            @error('base_price')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="down_payment" class="block text-sm font-medium">Down payment (₱)</label>
            <input id="down_payment" name="down_payment" type="number" step="0.01" min="0" value="{{ old('down_payment', $venue?->down_payment ?? 0) }}" class="mt-1 w-full rounded border-gray-300">
            @error('down_payment')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label for="gcash_number" class="block text-sm font-medium">GCash number</label>
        <input id="gcash_number" name="gcash_number" type="text" placeholder="09XXXXXXXXX" value="{{ old('gcash_number', $venue?->gcash_number) }}" class="mt-1 w-full rounded border-gray-300">
        @error('gcash_number')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <span class="block text-sm font-medium mb-1">Amenities</span>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            @foreach ($amenities as $amenity)
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" @checked(in_array($amenity->id, $selected))>
                    {{ $amenity->name }}
                </label>
            @endforeach
        </div>
    </div>
</div>