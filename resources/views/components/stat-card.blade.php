@props(['label', 'value', 'href' => null, 'highlight' => false])
@php($tag = $href ? 'a' : 'div')
<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    class="block rounded bg-white shadow p-4 {{ $highlight ? 'ring-2 ring-yellow-400' : '' }}">
    <div class="text-sm text-gray-500">{{ $label }}</div>
    <div class="text-3xl font-semibold mt-1">{{ $value }}</div>
</{{ $tag }}>