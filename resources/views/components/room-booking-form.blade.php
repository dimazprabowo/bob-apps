@props(['editMode' => false, 'rooms' => []])

<div class="space-y-4">
    <x-booking-guest-info color="emerald" />

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Ruangan <span class="text-red-500">*</span>
        </label>
        <div class="mt-1">
            <x-searchable-select
                wire:model="room_id"
                :options="$rooms"
                placeholder="Pilih ruangan..."
                searchPlaceholder="Cari ruangan..."
                :error="$errors->has('room_id')"
            />
        </div>
        @error('room_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Tanggal <span class="text-red-500">*</span>
        </label>
        <input wire:model="booking_form_date" type="date"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white">
        @error('booking_form_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Jam Mulai <span class="text-red-500">*</span>
            </label>
            <input wire:model="start_time" type="time"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white">
            @error('start_time') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Jam Selesai <span class="text-red-500">*</span>
            </label>
            <input wire:model="end_time" type="time"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white">
            @error('end_time') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Tujuan / Keperluan <span class="text-red-500">*</span>
        </label>
        <input wire:model="purpose" type="text"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white"
            placeholder="Tujuan penggunaan ruangan">
        @error('purpose') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Jumlah Peserta <span class="text-red-500">*</span>
        </label>
        <input wire:model="participants" type="number" min="1" max="200"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white"
            placeholder="Jumlah peserta">
        @error('participants') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan (Opsional)</label>
        <textarea wire:model="notes" rows="3"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white"
            placeholder="Catatan tambahan"></textarea>
        @error('notes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
    </div>
</div>
