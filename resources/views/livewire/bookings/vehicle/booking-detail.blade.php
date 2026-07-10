<div>
    <div class="mb-6" x-data="{ loading: false }">
        <button type="button" @click="loading = true; $wire.goBack()"
            :disabled="loading"
            class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors disabled:opacity-50">
            <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <svg x-show="loading" class="animate-spin w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Kembali ke Daftar Booking
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Detail Booking Armada</h2>
                        <p class="text-sm font-mono text-blue-600 dark:text-blue-400">{{ $booking->booking_code }}</p>
                    </div>
                    @php $color = $booking->status->color(); @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-400">
                        {{ $booking->status->label() }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Kendaraan</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->vehicle?->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->vehicle?->plate_number }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Kategori</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->vehicle?->category?->label() ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tanggal Booking</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->booking_date->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $booking->duration }} hari (s/d {{ $booking->end_date->format('d M Y') }})</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tujuan</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->destination }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Driver</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->driver ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Catatan</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $booking->notes ?? '-' }}</p>
                    </div>
                </div>
            </div>

            @if($booking->notes && $booking->status->value === 'rejected')
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <p class="text-xs text-red-700 dark:text-red-300 uppercase tracking-wider mb-1">Alasan Penolakan</p>
                    <p class="text-sm text-red-900 dark:text-red-200">{{ $booking->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Informasi Peminjam</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Nama</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->booker_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Divisi</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $booking->booker_divisi ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">No. HP</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $booking->booker_phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Email</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $booking->booker_email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tipe</p>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $booking->isGuest() ? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' }}">
                            {{ $booking->isGuest() ? 'Guest' : 'Registered User' }}
                        </span>
                    </div>
                </div>
            </div>

            @if($booking->approver)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Approval</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Approved/Rejected By</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $booking->approver->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Tanggal</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $booking->approved_at?->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            @can('approve', $booking)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Aksi</h3>
                    <div class="space-y-2">
                        @if($booking->status->value === 'pending')
                            <x-loading-button wire:click="confirmApprove" target="confirmApprove" variant="success" size="md" class="w-full" loadingText="Memuat...">
                                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></x-slot:icon>
                                Setujui
                            </x-loading-button>
                            <x-loading-button wire:click="confirmReject" target="confirmReject" variant="danger" size="md" class="w-full" loadingText="Memuat...">
                                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></x-slot:icon>
                                Tolak
                            </x-loading-button>
                        @endif
                        @if($booking->status->value === 'approved')
                            <x-loading-button wire:click="complete" target="complete" variant="primary" size="md" class="w-full" loadingText="Memuat...">
                                <x-slot:icon><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-slot:icon>
                                Selesaikan
                            </x-loading-button>
                        @endif
                    </div>
                </div>
            @endcan
        </div>
    </div>

    <x-approve-booking-modal :show="$showApproveModal">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Driver <span class="text-gray-400 text-xs">(opsional)</span>
                </label>
                <input wire:model="approveDriver" type="text"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Nama driver">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Catatan <span class="text-gray-400 text-xs">(opsional)</span>
                </label>
                <textarea wire:model="approveNotes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-emerald-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Catatan tambahan"></textarea>
            </div>
        </div>
    </x-approve-booking-modal>

    <x-reject-booking-modal :show="$showRejectModal" />
</div>
