@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'loadingText' => null,
])

@php
    $variants = [
        'primary'   => 'bg-blue-600 hover:bg-blue-700 text-white',
        'success'   => 'bg-emerald-600 hover:bg-emerald-700 text-white',
        'danger'    => 'bg-red-600 hover:bg-red-700 text-white',
        'warning'   => 'bg-yellow-600 hover:bg-yellow-700 text-white',
        'purple'    => 'bg-purple-600 hover:bg-purple-700 text-white',
        'secondary' => 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300',
    ];

    $sizes = [
        'xs' => 'px-2 py-1 text-xs gap-1',
        'sm' => 'px-2.5 py-1.5 text-sm gap-1',
        'md' => 'px-3 py-2 text-sm gap-1.5',
        'lg' => 'px-6 py-2.5 text-sm gap-2',
    ];

    $spinnerSizes = [
        'xs' => 'h-3 w-3',
        'sm' => 'h-3.5 w-3.5',
        'md' => 'h-4 w-4',
        'lg' => 'h-4 w-4',
    ];

    $hasIcon = isset($icon) && !$icon->isEmpty();
    $variantClass = $variants[$variant] ?? $variants['primary'];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $spinnerSize = $spinnerSizes[$size] ?? $spinnerSizes['md'];
@endphp

<a
    href="{{ $href }}"
    wire:navigate
    x-data="{ loading: false }"
    x-on:click="loading = true"
    x-on:livewire:navigated.window="loading = false"
    :class="{ 'opacity-50 cursor-not-allowed pointer-events-none': loading }"
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center font-semibold rounded-lg transition-colors whitespace-nowrap {$variantClass} {$sizeClass}"]) }}
>
    <svg x-show="loading" x-cloak class="animate-spin {{ $spinnerSize }}" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>

    @if($hasIcon)
        <span x-show="!loading">{{ $icon }}</span>
    @endif

    @if($loadingText)
        <span x-show="!loading">{{ $slot }}</span>
        <span x-show="loading" x-cloak>{{ $loadingText }}</span>
    @else
        <span x-show="!loading">{{ $slot }}</span>
    @endif
</a>
