@props([
    'color' => 'blue',
])

@php
    $bgClasses = [
        'blue'    => 'bg-blue-50 dark:bg-blue-900/20',
        'purple'  => 'bg-purple-50 dark:bg-purple-900/20',
        'emerald' => 'bg-emerald-50 dark:bg-emerald-900/20',
        'indigo'  => 'bg-indigo-50 dark:bg-indigo-900/20',
    ];
    $labelClasses = [
        'blue'    => 'text-blue-700 dark:text-blue-300',
        'purple'  => 'text-purple-700 dark:text-purple-300',
        'emerald' => 'text-emerald-700 dark:text-emerald-300',
        'indigo'  => 'text-indigo-700 dark:text-indigo-300',
    ];
    $ringClasses = [
        'blue'    => 'focus:ring-blue-500',
        'purple'  => 'focus:ring-purple-500',
        'emerald' => 'focus:ring-emerald-500',
        'indigo'  => 'focus:ring-indigo-500',
    ];
    $bgClass = $bgClasses[$color] ?? $bgClasses['blue'];
    $labelClass = $labelClasses[$color] ?? $labelClasses['blue'];
    $ringClass = $ringClasses[$color] ?? $ringClasses['blue'];
@endphp

<div class="{{ $bgClass }} rounded-lg p-4 mb-2">
    <p class="text-xs {{ $labelClass }} font-medium mb-3">Informasi Pembooking</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input wire:model="guest_name" type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 {{ $ringClass }} dark:bg-gray-700 dark:text-white"
                placeholder="Nama lengkap">
            @error('guest_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                No. HP / WhatsApp <span class="text-red-500">*</span>
            </label>
            <input wire:model="guest_phone" type="tel"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 {{ $ringClass }} dark:bg-gray-700 dark:text-white"
                placeholder="08xxxxxxxxxx">
            @error('guest_phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Divisi / Departemen <span class="text-red-500">*</span>
            </label>
            <input wire:model="guest_divisi" type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 {{ $ringClass }} dark:bg-gray-700 dark:text-white"
                placeholder="Divisi / Departemen">
            @error('guest_divisi') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email (Opsional)</label>
            <input wire:model="guest_email" type="email"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 {{ $ringClass }} dark:bg-gray-700 dark:text-white"
                placeholder="email@example.com">
            @error('guest_email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
    </div>
</div>
