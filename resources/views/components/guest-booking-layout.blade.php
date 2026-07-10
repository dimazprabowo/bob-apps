@props([
    'color' => 'blue',
])

@php
    $gradients = [
        'blue'    => 'from-blue-600 via-blue-700 to-blue-900 dark:from-blue-800 dark:via-blue-900 dark:to-gray-900',
        'purple'  => 'from-purple-600 via-purple-700 to-purple-900 dark:from-purple-800 dark:via-purple-900 dark:to-gray-900',
        'emerald' => 'from-emerald-600 via-emerald-700 to-emerald-900 dark:from-emerald-800 dark:via-emerald-900 dark:to-gray-900',
        'indigo'  => 'from-indigo-600 via-indigo-700 to-indigo-900 dark:from-indigo-800 dark:via-indigo-900 dark:to-gray-900',
    ];
    $textColors = [
        'blue'    => 'text-blue-100',
        'purple'  => 'text-purple-100',
        'emerald' => 'text-emerald-100',
        'indigo'  => 'text-indigo-100',
    ];
    $gradientClass = $gradients[$color] ?? $gradients['blue'];
    $textColorClass = $textColors[$color] ?? $textColors['blue'];
@endphp

<div class="min-h-screen flex flex-col lg:flex-row relative">
    {{-- Dark Mode Toggle --}}
    <div class="fixed top-4 right-4 z-50">
        <button @click="document.documentElement.classList.toggle('dark'); localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'))"
                class="p-3 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 border border-gray-200 dark:border-gray-700">
            <svg class="w-5 h-5 dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
            </svg>
            <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </button>
    </div>

    {{-- Left Side - Branding --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-2/5 bg-gradient-to-br {{ $gradientClass }} p-8 lg:p-12 flex-col justify-between relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-white rounded-full translate-y-1/2 -translate-x-1/2"></div>
        </div>

        <div class="relative z-10">
            <div class="flex items-center space-x-3 mb-12">
                <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-lg p-1.5 overflow-hidden">
                    <img src="{{ asset('images/bki-main.webp') }}" alt="BKI Logo" class="w-full h-full object-contain rounded-lg">
                </div>
                <div class="text-white">
                    <h1 class="text-2xl lg:text-3xl font-bold">{{ config('app.name', 'Boilerplate') }}</h1>
                    <p class="text-sm {{ $textColorClass }}">PT. Biro Klasifikasi Indonesia</p>
                </div>
            </div>
            {{ $branding }}
        </div>
        <div class="relative z-10 {{ $textColorClass }} text-sm">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Boilerplate') }}. All rights reserved.</p>
        </div>
    </div>

    {{-- Right Side - Content --}}
    <div class="flex-1 flex items-start lg:items-center justify-center p-4 sm:p-6 lg:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen lg:min-h-0 overflow-y-auto">
        <div class="w-full max-w-lg py-8">

            {{-- Mobile Logo --}}
            <div class="lg:hidden flex flex-col items-center mb-8">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-lg p-1.5 overflow-hidden">
                        <img src="{{ asset('images/bki-main.webp') }}" alt="BKI Logo" class="w-full h-full object-contain rounded-lg">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ config('app.name', 'Boilerplate') }}</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">PT. Biro Klasifikasi Indonesia</p>
                    </div>
                </div>
            </div>

            {{ $slot }}
        </div>
    </div>
</div>
