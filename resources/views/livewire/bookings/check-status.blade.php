<x-guest-booking-layout color="indigo">
    <x-slot:branding>
        <div class="space-y-6 max-w-lg">
            <div class="flex items-center gap-4 mb-2">
                <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h2 class="text-3xl lg:text-4xl font-bold text-white">Cek Status Booking</h2>
            </div>
            <p class="text-lg text-indigo-100 leading-relaxed">Lacak status booking Anda dengan mudah menggunakan kode booking, nomor HP, atau nama.</p>
            <div class="space-y-4 pt-4">
                <div class="flex items-start space-x-4 text-indigo-50">
                    <div class="flex-shrink-0 w-8 h-8 bg-indigo-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white mb-1">Pencarian Fleksibel</h3>
                        <p class="text-sm text-indigo-200">Cari dengan kode booking, nomor HP, atau nama</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 text-indigo-50">
                    <div class="flex-shrink-0 w-8 h-8 bg-indigo-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white mb-1">Semua Jenis Booking</h3>
                        <p class="text-sm text-indigo-200">Armada, Meeting Online, dan Ruangan</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 text-indigo-50">
                    <div class="flex-shrink-0 w-8 h-8 bg-indigo-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white mb-1">Status Real-Time</h3>
                        <p class="text-sm text-indigo-200">Pending, Approved, Rejected, Completed</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:branding>

    {{-- Search Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 sm:p-8 border border-gray-200 dark:border-gray-700 mb-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Cek Status</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm">Masukkan kode booking, nomor HP, atau nama untuk melacak booking Anda</p>
        </div>
        <form wire:submit.prevent="search" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Kode Booking / Nomor HP / Nama <span class="text-red-500">*</span>
                </label>
                <input wire:model="query" type="text"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    placeholder="VHC-20250101-001 atau 08xxxxxxxxxx atau nama">
                @error('query') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            <x-loading-button type="submit" target="search" variant="primary" size="md" loadingText="Mencari..." class="w-full">
                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg></x-slot:icon>
                Cek Status
            </x-loading-button>
        </form>
    </div>

    @if($searched)
        <div class="space-y-4">
            @if($vehicleBookings->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8m-8 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0m12 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0M3 17V8a1 1 0 011-1h10v10M14 7h3.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V17"/></svg>
                        Booking Armada
                    </h3>
                    <div class="space-y-3">
                        @foreach($vehicleBookings as $booking)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div>
                                    <p class="text-sm font-mono font-semibold text-gray-900 dark:text-white">{{ $booking->booking_code }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->vehicle?->name }} · {{ $booking->booking_date->format('d M Y') }}</p>
                                </div>
                                @php $color = $booking->status->color(); @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-400">{{ $booking->status->label() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($zoomBookings->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Booking Meeting Online
                    </h3>
                    <div class="space-y-3">
                        @foreach($zoomBookings as $booking)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div>
                                    <p class="text-sm font-mono font-semibold text-gray-900 dark:text-white">{{ $booking->booking_code }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->topic }} · {{ $booking->booking_date->format('d M Y') }}</p>
                                </div>
                                @php $color = $booking->status->color(); @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-400">{{ $booking->status->label() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($roomBookings->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Booking Ruangan
                    </h3>
                    <div class="space-y-3">
                        @foreach($roomBookings as $booking)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div>
                                    <p class="text-sm font-mono font-semibold text-gray-900 dark:text-white">{{ $booking->booking_code }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->room?->name }} · {{ $booking->booking_date->format('d M Y') }}</p>
                                </div>
                                @php $color = $booking->status->color(); @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-400">{{ $booking->status->label() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($vehicleBookings->isEmpty() && $zoomBookings->isEmpty() && $roomBookings->isEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                    <div class="w-14 h-14 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada booking ditemukan untuk pencarian tersebut.</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Footer Links --}}
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            <x-navigate-link href="{{ route('landing') }}" color="indigo">Beranda</x-navigate-link>
            <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
            <x-navigate-link href="{{ route('booking.armada.form') }}" color="indigo">Booking Armada</x-navigate-link>
            <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
            <x-navigate-link href="{{ route('booking.zoom.form') }}" color="indigo">Booking Zoom</x-navigate-link>
            <span class="mx-2 text-gray-300 dark:text-gray-600">|</span>
            <x-navigate-link href="{{ route('booking.ruangan.form') }}" color="indigo">Booking Ruangan</x-navigate-link>
        </p>
    </div>
</x-guest-booking-layout>