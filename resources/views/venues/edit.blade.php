<x-layouts.public title="Edit venue">
    <h1 class="text-2xl font-semibold mb-4">Edit {{ $venue->name }}</h1>

    <form method="POST" action="{{ route('venues.update', $venue) }}" class="bg-white rounded shadow p-6">
        @csrf
        @method('PUT')
        @include('venues._form')

        <div class="mt-6 flex gap-3">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded">Update</button>
            <a href="{{ route('venues.show', $venue) }}" class="px-4 py-2 underline">Cancel</a>
        </div>
    </form>
</x-layouts.public>