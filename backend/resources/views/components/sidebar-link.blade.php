@props(['href' => '#', 'active' => false'])

@php
    $classes = $active
        ? 'bg-gray-800 text-white'
        : 'text-gray-400 hover:bg-gray-800 hover:text-white';
@endphp

<li>
    <a href="{{ $href }}" class="{{ $classes }} flex items-center gap-x-3 rounded-md px-3 py-2 text-sm font-medium leading-6 transition-colors">
        {{ $slot }}
    </a>
</li>
