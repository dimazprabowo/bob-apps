<x-guest-booking-layout color="emerald">
    <x-slot:branding>
        <div class="space-y-6 max-w-lg">
            <div class="flex items-center gap-4 mb-2">
                <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h2 class="text-3xl lg:text-4xl font-bold text-white">Booking Ruangan</h2>
            </div>
            <p class="text-lg text-emerald-100 leading-relaxed">Ajukan peminjaman ruangan untuk rapat atau kegiatan internal.</p>
            <div class="space-y-4 pt-4">
                <div class="flex items-start space-x-4 text-emerald-50">
                    <div class="flex-shrink-0 w-8 h-8 bg-emerald-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white mb-1">Berbagai Pilihan Ruangan</h3>
                        <p class="text-sm text-emerald-200">Dengan kapasitas dan lokasi berbeda</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 text-emerald-50">
                    <div class="flex-shrink-0 w-8 h-8 bg-emerald-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white mb-1">Real-Time Availability</h3>
                        <p class="text-sm text-emerald-200">Cek ketersediaan ruangan secara langsung</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 text-emerald-50">
                    <div class="flex-shrink-0 w-8 h-8 bg-emerald-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white mb-1">Konflik Otomatis</h3>
                        <p class="text-sm text-emerald-200">Sistem mendeteksi jadwal yang bentrok</p>
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
                        <span class="text-xl font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ $successBookingCode }}</span>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-6">Simpan kode booking untuk cek status.</p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('booking.check-status') }}" wire:navigate class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition-colors inline-flex items-center justify-center gap-2">Cek Status</a>
                        <button wire:click="$set('showSuccess', false)" class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">Booking Lagi</button>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Formulir Booking</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm">Lengkapi data di bawah untuk mengajukan booking ruangan</p>
                    </div>

                    <form wire:submit.prevent="submit" class="space-y-5">

                        <x-booking-honeypot />

                        <x-booking-guest-info color="emerald" />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Ruangan <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <x-searchable-select
                                    wire:model.live="room_id"
                                    :options="$rooms"
                                    placeholder="Pilih ruangan..."
                                    searchPlaceholder="Cari ruangan..."
                                    :error="$errors->has('room_id')"
                                />
                            </div>
                            @error('room_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <livewire:bookings.booking-availability :type="'room'" :resourceId="$room_id" :key="'room-avail-' . $room_id" />

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="booking_date" type="date"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                            @error('booking_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Jam Mulai <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="start_time" type="time"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('start_time') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Jam Selesai <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="end_time" type="time"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                @error('end_time') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tujuan / Keperluan <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="purpose" type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Tujuan penggunaan ruangan">
                            @error('purpose') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Jumlah Peserta <span class="text-red-500">*</span>
                            </label>
                            <input wire:model="participants" type="number" min="1" max="200"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Jumlah peserta">
                            @error('participants') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
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
                                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></x-slot:icon>
                                Ajukan Booking
                            </x-loading-button>
                            <a href="{{ route('booking.check-status') }}" wire:navigate class="px-6 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-center">
                                Cek Status Booking
                            </a>
                        </div>
                    </form>
                </div>
            @endif

    {{-- Footer Links --}}
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            @if(auth()->check())
                <a href="{{ route('dashboard') }}" wire:navigate class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium transition-colors">Kembali ke Dashboard</a>
            @else
                <a href="{{ route('landing') }}" wire:navigate class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium transition-colors">Beranda</a>
                <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                <a href="{{ route('login') }}" wire:navigate class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium transition-colors">Login</a>
                <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                <a href="{{ route('booking.armada.form') }}" wire:navigate class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium transition-colors">Booking Armada</a>
                <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
                <a href="{{ route('booking.zoom.form') }}" wire:navigate class="text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium transition-colors">Booking Zoom</a>
            @endif
        </p>
    </div>
</x-guest-booking-layout>