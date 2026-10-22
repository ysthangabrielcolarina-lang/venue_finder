<x-layouts.public :title="'Reserve '.$venue->name">
    <a href="{{ route('venues.show', $venue) }}" class="text-sm text-gray-600 hover:text-indigo-700">&larr; Back to {{ $venue->name }}</a>

    <h1 class="mt-3 text-2xl font-semibold">Request to reserve</h1>
    <p class="mt-1 text-gray-600">Send your dates to the owner. They confirm or decline within the 24-hour hold.</p>

    <div class="mt-6 grid gap-8 lg:grid-cols-3">
        <form method="POST" action="{{ route('reservations.store', $venue) }}" enctype="multipart/form-data" class="space-y-6 lg:col-span-2">
            @csrf

            <section class="rounded-2xl border border-gray-200 bg-white p-6">
                <h2 class="font-semibold">1. Choose your dates</h2>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="start_date" class="block text-sm font-medium">Start date</label>
                        <input id="start_date" name="start_date" type="date" value="{{ old('start_date') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium">End date</label>
                        <input id="end_date" name="end_date" type="date" value="{{ old('end_date') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('end_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="guest_count" class="block text-sm font-medium">Number of guests (up to {{ $venue->capacity }})</label>
                    <input id="guest_count" name="guest_count" type="number" min="1" value="{{ old('guest_count') }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('guest_count')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6">
                <h2 class="font-semibold">2. Payment</h2>

                @if ($venue->down_payment > 0)
                    <div class="mt-4 rounded-xl bg-indigo-50 p-4 text-sm text-indigo-900">
                        <p class="font-medium">Pay the down payment by GCash</p>
                        <p class="mt-1">
                            Send ₱{{ number_format($venue->down_payment, 2) }} to
                            <strong>{{ $venue->gcash_number ?: 'the owner (ask for the number)' }}</strong>,
                            then upload your receipt below. The owner checks it by hand.
                        </p>
                    </div>
                @else
                    <p class="mt-4 text-sm text-gray-600">This venue does not ask for a down payment. You can upload a receipt if you have one.</p>
                @endif

                <div class="mt-4">
                    <label for="receipt" class="block text-sm font-medium">Payment receipt {{ $venue->down_payment > 0 ? '' : '(optional)' }}</label>
                    <input id="receipt" name="receipt" type="file" accept="image/*" class="mt-1 block w-full text-sm">
                    @error('receipt')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </section>

            <div class="flex gap-3">
                <x-btn>Send request</x-btn>
                <x-btn :href="route('venues.show', $venue)" variant="secondary">Cancel</x-btn>
            </div>
        </form>

        <aside class="lg:col-span-1">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm lg:sticky lg:top-44">
                <p class="text-lg font-semibold">{{ $venue->name }}</p>
                <p class="text-sm text-gray-500">{{ $venue->barangay->name }}</p>

                <dl class="mt-4 space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Capacity</dt><dd>{{ $venue->capacity }} guests</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Base price</dt><dd>₱{{ number_format($venue->base_price) }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Down payment</dt><dd>₱{{ number_format($venue->down_payment) }}</dd></div>
                </dl>

                <ol class="mt-6 space-y-2 border-t border-gray-100 pt-4 text-sm text-gray-600">
                    <li>1. You send the request.</li>
                    <li>2. Your dates are held for 24 hours.</li>
                    <li>3. The owner confirms or declines.</li>
                </ol>
            </div>
        </aside>
    </div>
</x-layouts.public>