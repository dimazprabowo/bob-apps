<x-guest-booking-layout color="blue">
    <x-slot:branding>
        <div class="space-y-6 max-w-lg">
            <h2 class="text-3xl lg:text-4xl xl:text-5xl font-bold text-white leading-tight">
                Sistem Booking Terpadu
            </h2>
            <p class="text-lg lg:text-xl text-blue-100 leading-relaxed">
                Ajukan dan pantau booking armada, meeting online, dan ruangan dalam satu platform.
            </p>
            <div class="space-y-4 pt-8">
                <div class="flex items-start space-x-4 text-blue-50">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8m-8 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0m12 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0M3 17V8a1 1 0 011-1h10v10M14 7h3.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V17"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white mb-1">Booking Armada</h3>
                        <p class="text-sm text-blue-200">Pinjam kendaraan untuk keperluan dinas</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 text-blue-50">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white mb-1">Booking Meeting Online</h3>
                        <p class="text-sm text-blue-200">Zoom, Google Meet, Microsoft Teams</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4 text-blue-50">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-500/30 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white mb-1">Booking Ruangan</h3>
                        <p class="text-sm text-blue-200">Rapat dan kegiatan internal</p>
                    </div>
                </div>
            </div>
        </div>
    </x-slot:branding>

    {{-- Header --}}
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Pilih Jenis Booking</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm">Silakan pilih layanan booking yang Anda butuhkan</p>
                </div>

                <!-- Booking Cards -->
                <div class="space-y-4 mb-6">

                    {{-- Armada --}}
                    <a href="{{ route('booking.armada.form') }}" wire:navigate
                       x-data="{ loading: false }"
                       x-on:click="loading = true"
                       x-on:livewire:navigated.window="loading = false"
                       :class="{ 'opacity-50 pointer-events-none': loading }"
                       class="group flex items-center gap-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg hover:border-blue-400 dark:hover:border-blue-500 transition-all">
                        <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-600 to-blue-800 rounded-xl flex items-center justify-center text-white shadow-md">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h8m-8 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0m12 0a2 2 0 11-4 0m4 0a2 2 0 10-4 0M3 17V8a1 1 0 011-1h10v10M14 7h3.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V17"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Booking Armada</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pinjam kendaraan untuk keperluan dinas</p>
                        </div>
                        <svg x-show="!loading" class="w-5 h-5 text-gray-400 transition-transform group-hover:translate-x-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        <svg x-show="loading" x-cloak class="animate-spin w-5 h-5 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </a>

                    {{-- Zoom --}}
                    <a href="{{ route('booking.zoom.form') }}" wire:navigate
                       x-data="{ loading: false }"
                       x-on:click="loading = true"
                       x-on:livewire:navigated.window="loading = false"
                       :class="{ 'opacity-50 pointer-events-none': loading }"
                       class="group flex items-center gap-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg hover:border-purple-400 dark:hover:border-purple-500 transition-all">
                        <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-purple-600 to-purple-800 rounded-xl flex items-center justify-center text-white shadow-md">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Booking Meeting Online</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Zoom, Google Meet, Microsoft Teams</p>
                        </div>
                        <svg x-show="!loading" class="w-5 h-5 text-gray-400 transition-transform group-hover:translate-x-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        <svg x-show="loading" x-cloak class="animate-spin w-5 h-5 text-purple-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </a>

                    {{-- Ruangan --}}
                    <a href="{{ route('booking.ruangan.form') }}" wire:navigate
                       x-data="{ loading: false }"
                       x-on:click="loading = true"
                       x-on:livewire:navigated.window="loading = false"
                       :class="{ 'opacity-50 pointer-events-none': loading }"
                       class="group flex items-center gap-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg hover:border-emerald-400 dark:hover:border-emerald-500 transition-all">
                        <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-xl flex items-center justify-center text-white shadow-md">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">Booking Ruangan</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Rapat dan kegiatan internal</p>
                        </div>
                        <svg x-show="!loading" class="w-5 h-5 text-gray-400 transition-transform group-hover:translate-x-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        <svg x-show="loading" x-cloak class="animate-spin w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </a>
                </div>

                <!-- Check Status -->
                <a href="{{ route('booking.check-status') }}" wire:navigate
                   x-data="{ loading: false }"
                   x-on:click="loading = true"
                   x-on:livewire:navigated.window="loading = false"
                   :class="{ 'opacity-50 pointer-events-none': loading }"
                   class="group flex items-center gap-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl border border-indigo-200 dark:border-indigo-800 p-5 hover:shadow-lg hover:border-indigo-400 dark:hover:border-indigo-500 transition-all mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 dark:bg-indigo-900/40 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-bold text-indigo-900 dark:text-indigo-200">Cek Status Booking</h3>
                        <p class="text-sm text-indigo-600 dark:text-indigo-400">Lacak status booking Anda dengan kode booking, nomor HP, atau nama</p>
                    </div>
                    <svg x-show="!loading" class="w-5 h-5 text-indigo-400 transition-transform group-hover:translate-x-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    <svg x-show="loading" x-cloak class="animate-spin w-5 h-5 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </a>

    {{-- Footer Links --}}
    <div class="text-center pt-6 border-t border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            @if(auth()->check())
                <x-navigate-link href="{{ route('dashboard') }}" color="blue">Dashboard</x-navigate-link>
            @else
                <x-navigate-link href="{{ route('login') }}" color="blue">Login</x-navigate-link>
            @endif
        </p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">&copy; {{ date('Y') }} PT. Biro Klasifikasi Indonesia</p>
    </div>
</x-guest-booking-layout>
