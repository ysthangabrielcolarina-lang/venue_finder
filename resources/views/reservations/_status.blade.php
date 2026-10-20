@php
    $classes = [
        'pending'   => 'bg-yellow-100 text-yellow-800',
        'confirmed' => 'bg-green-100 text-green-800',
        'rejected'  => 'bg-red-100 text-red-800',
        'cancelled' => 'bg-gray-200 text-gray-700',
        'expired'   => 'bg-gray-200 text-gray-700',
    ];
@endphp
<span class="text-xs px-2 py-1 rounded {{ $classes[$status] ?? 'bg-gray-200 text-gray-700' }}">{{ ucfirst($status) }}</span>