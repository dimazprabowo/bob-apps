@props([
    'color' => 'blue',
])

<div class="bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20 rounded-lg p-4 mb-2">
    <p class="text-xs text-{{ $color }}-700 dark:text-{{ $color }}-300 font-medium mb-3">Informasi Pembooking</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input wire:model="guest_name" type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Nama lengkap">
            @error('guest_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                No. HP / WhatsApp <span class="text-red-500">*</span>
            </label>
            <input wire:model="guest_phone" type="tel"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="08xxxxxxxxxx">
            @error('guest_phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Divisi / Departemen <span class="text-red-500">*</span>
            </label>
            <input wire:model="guest_divisi" type="text"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Divisi / Departemen">
            @error('guest_divisi') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email (Opsional)</label>
            <input wire:model="guest_email" type="email"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="email@example.com">
            @error('guest_email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
    </div>
</div>
