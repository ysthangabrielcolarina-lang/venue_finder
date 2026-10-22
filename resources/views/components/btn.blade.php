@props(['href' => null, 'variant' => 'primary', 'type' => 'submit'])
@php
    $base = 'inline-flex items-center justify-center h-9 px-4 rounded-md text-sm font-medium whitespace-nowrap transition';

    $styles = [
        'primary'   => 'bg-indigo-600 text-white hover:bg-indigo-700',
        'secondary' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
        'success'   => 'bg-green-600 text-white hover:bg-green-700',
        'danger'    => 'bg-red-600 text-white hover:bg-red-700',
    ];

    $class = $base.' '.($styles[$variant] ?? $styles['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
@endif