@props([
    'href' => null,
    'color' => 'blue',
])

@php
    $colors = [
        'blue'    => 'text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300',
        'emerald' => 'text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300',
        'purple'  => 'text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300',
        'indigo'  => 'text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300',
    ];

    $colorClass = $colors[$color] ?? $colors['blue'];
@endphp

<a
    href="{{ $href }}"
    wire:navigate
    x-data="{ loading: false }"
    x-on:click="loading = true"
    x-on:livewire:navigated.window="loading = false"
    :class="{ 'opacity-50 pointer-events-none': loading }"
    {{ $attributes->merge(['class' => "font-medium transition-colors inline-flex items-center gap-1 {$colorClass}"]) }}
>
    <svg x-show="loading" x-cloak class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <span x-show="!loading">{{ $slot }}</span>
</a>
