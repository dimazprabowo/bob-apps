<x-guest-booking-layout color="blue">
    <x-slot:branding>
        <div class="space-y-6 max-w-lg">
            <div class="flex items-center gap-4 mb-2">
                <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8m-8 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0m12 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0M3 17V8a1 1 0 011-1h10v10M14 7h3.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V17"/></svg>
                </div>
                <h2 class="text-3xl lg:text-4xl font-bold text-white">Booking Armada</h2>
            </div>
            <p class="text-lg text-blue-100 leading-relaxed">Ajukan peminjaman kendaraan untuk keperluan dinas dengan mudah dan cepat.</p>
            <div class="space-y-4 pt-4">
                <div class="flex items-start space-x-4 text-blue-50">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white mb-1">Pilih Kendaraan Tersedia</h3>
                        <p class="text-sm text-blue-200">Berbagai pilihan armada dengan status real-time</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 text-blue-50">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white mb-1">Proses Cepat & Transparan</h3>
                        <p class="text-sm text-blue-200">Tracking status booking dengan kode unik</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 text-blue-50">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white mb-1">Konfirmasi Otomatis</h3>
                        <p class="text-sm text-blue-200">Notifikasi via WhatsApp atau email</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:branding>

    @if($showSuccess)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 border border-gray-200 dark:border-gray-700 text-center">
                    <div class="w-16 h-16 mx-auto bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Booking Berhasil Diajukan!</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Kode booking Anda:</p>
                    <div class="inline-block bg-gray-100 dark:bg-gray-700 rounded-lg px-6 py-3 mb-4">
                        <span class="text-xl font-mono font-bold text-blue-600 dark:text-blue-400">{{ $successBookingCode }}</span>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-6">Simpan kode booking untuk cek status. Anda akan dihubungi oleh admin untuk konfirmasi.</p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <x-navigate-button href="{{ route('booking.check-status') }}" variant="primary" size="lg" loadingText="Memuat..." class="flex-1 sm:flex-none">
                            Cek Status
                        </x-navigate-button>
                        <x-loading-button wire:click="resetForm" target="resetForm" variant="secondary" size="lg" loadingText="Memuat..." class="flex-1 sm:flex-none">
                            Booking Lagi
                        </x-loading-button>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Formulir Booking</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm">Lengkapi data di bawah untuk mengajukan booking armada</p>
                    </div>

                    <form wire:submit.prevent="submit" class="space-y-5">

                        <x-booking-honeypot />

                        <x-booking-guest-info color="blue" />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Kendaraan <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <x-searchable-select
                                    wire:model.live="vehicle_id"
                                    :options="$vehicles"
                                    placeholder="Pilih kendaraan..."
                                    searchPlaceholder="Cari kendaraan..."
                                    :error="$errors->has('vehicle_id')"
                                />
                            </div>
                            @error('vehicle_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <livewire:bookings.booking-availability :type="'vehicle'" :resourceId="$vehicle_id" :duration="(int)$duration" :bookingDate="$booking_date" :key="'vehicle-avail-' . $vehicle_id" />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Tanggal Booking <span class="text-red-500">*</span>
                                </label>
                                <input wire:model.live="booking_date" type="date"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('booking_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Durasi (Hari) <span class="text-red-500">*</span>
                                </label>
                                <input wire:model.live="duration" type="number" min="1" max="30"
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

                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <x-loading-button type="submit" target="submit" variant="primary" size="md" loadingText="Mengirim..." class="flex-1">
                                <x-slot:icon>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                </x-slot:icon>
                                Ajukan Booking
                            </x-loading-button>
                            <x-navigate-button href="{{ route('booking.check-status') }}" variant="secondary" size="lg" loadingText="Memuat..." class="flex-1">
                                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></x-slot:icon>
                                Cek Status Booking
                            </x-navigate-button>
                        </div>
                    </form>
                </div>
            @endif

    {{-- Footer Links --}}
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            @if(auth()->check())
                <x-navigate-link href="{{ route('dashboard') }}" color="blue">Kembali ke Dashboard</x-navigate-link>
            @else
                <x-navigate-link href="{{ route('landing') }}" color="blue">Beranda</x-navigate-link>
                <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                <x-navigate-link href="{{ route('login') }}" color="blue">Login</x-navigate-link>
                <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                <x-navigate-link href="{{ route('booking.zoom.form') }}" color="blue">Booking Zoom</x-navigate-link>
                <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                <x-navigate-link href="{{ route('booking.ruangan.form') }}" color="blue">Booking Ruangan</x-navigate-link>
            @endif
        </p>
    </div>
</x-guest-booking-layout>