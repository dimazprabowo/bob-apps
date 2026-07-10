@props(['editMode' => false, 'vehicles' => []])

<div class="space-y-4">
    <x-booking-guest-info color="blue" />

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Kendaraan <span class="text-red-500">*</span>
        </label>
        <div class="mt-1">
            <x-searchable-select
                wire:model="vehicle_id"
                :options="$vehicles"
                placeholder="Pilih kendaraan..."
                searchPlaceholder="Cari kendaraan..."
                :error="$errors->has('vehicle_id')"
            />
        </div>
        @error('vehicle_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Tanggal Booking <span class="text-red-500">*</span>
            </label>
            <input wire:model="booking_form_date" type="date"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            @error('booking_form_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Durasi (Hari) <span class="text-red-500">*</span>
            </label>
            <input wire:model="duration" type="number" min="1" max="30"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="1">
            @error('duration') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Tujuan <span class="text-red-500">*</span>
        </label>
        <input wire:model="destination" type="text"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Tujuan penggunaan kendaraan">
        @error('destination') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan (Opsional)</label>
        <textarea wire:model="notes" rows="3"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            placeholder="Catatan tambahan"></textarea>
        @error('notes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>
</div>
