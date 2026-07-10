<x-app-layout title="Detail Laporan Ruangan">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Laporan Ruangan
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('reports.ruangan') }}" wire:navigate
                    x-data="{ loading: false }" @click="loading = true"
                    :class="{ 'opacity-50': loading }"
                    class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors">
                    <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <svg x-show="loading" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Kembali ke Laporan Ruangan
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Detail Booking Ruangan</h2>
                                <p class="text-sm font-mono text-blue-600 dark:text-blue-400">{{ $booking->booking_code }}</p>
                            </div>
                            @php $color = $booking->status->color(); @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-400">
                                {{ $booking->status->label() }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Ruangan</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->room?->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->room?->location }} · Kapasitas {{ $booking->room?->capacity }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Fasilitas</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $booking->room?->facilities_list }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tanggal</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->booking_date->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Waktu</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->time_range }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tujuan</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->purpose }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Jumlah Peserta</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->participants }} orang</p>
                            </div>
                            <div class="sm:col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Catatan</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $booking->notes ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Informasi Peminjam</h3>
                        <div class="space-y-3">
                            <div><p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Nama</p><p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->booker_name }}</p></div>
                            <div><p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Divisi</p><p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->booker_divisi ?? '-' }}</p></div>
                            <div><p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">No. HP</p><p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->booker_phone ?? '-' }}</p></div>
                            <div><p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Email</p><p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->booker_email ?? '-' }}</p></div>
                            <div><p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tipe</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $booking->isGuest() ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">{{ $booking->isGuest() ? 'Guest' : 'Registered User' }}</span>
                            </div>
                        </div>
                    </div>

                    @if($booking->approver)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Approval</h3>
                            <div class="space-y-3">
                                <div><p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Approved/Rejected By</p><p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->approver->name }}</p></div>
                                <div><p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tanggal</p><p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->approved_at?->format('d M Y H:i') }}</p></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
