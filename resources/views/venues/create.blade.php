<x-layouts.public title="Add venue">
    <h1 class="text-2xl font-semibold mb-4">Add venue</h1>

    <form method="POST" action="{{ route('venues.store') }}" class="bg-white rounded shadow p-6">
        @csrf
        @include('venues._form')

        <div class="mt-6 flex gap-3">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded">Save</button>
            <a href="{{ route('venues.index') }}" class="px-4 py-2 underline">Cancel</a>
        </div>
    </form>
</x-layouts.public>