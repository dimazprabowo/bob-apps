<div
    x-data="{
        conflictStatus: null,
        init() {
            Livewire.on('conflict-status', (event) => {
                this.conflictStatus = event.status;
            });
        }
    }"
    x-init="init()"
    class="bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-200 dark:border-gray-700 p-4"
>
    {{-- Calendar Header --}}
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Ketersediaan
        </h4>
        <div class="flex items-center gap-2">
            <button type="button" wire:click="previousMonth" wire:loading.attr="disabled" wire:target="previousMonth"
                class="p-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 min-w-[120px] text-center" wire:loading.remove wire:target="previousMonth,nextMonth,updatedCurrentMonth">
                {{ $this->calendar['monthName'] }}
            </span>
            <span class="text-sm text-gray-400" wire:loading wire:target="previousMonth,nextMonth,updatedCurrentMonth">
                <svg class="animate-spin w-4 h-4 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </span>
            <button type="button" wire:click="nextMonth" wire:loading.attr="disabled" wire:target="nextMonth"
                class="p-1.5 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors disabled:opacity-50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </div>

    @if(($type === 'vehicle' || $type === 'room') && !$resourceId)
        <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-8">
            Pilih {{ $type === 'vehicle' ? 'kendaraan' : 'ruangan' }} terlebih dahulu untuk melihat ketersediaan jadwal.
        </p>
    @else
        {{-- Day Headers --}}
        <div class="grid grid-cols-7 gap-1 mb-2">
            @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                <div class="text-center text-xs font-medium text-gray-400 dark:text-gray-500 py-1">{{ $day }}</div>
            @endforeach
        </div>

        {{-- Calendar Grid --}}
        <div class="relative grid grid-cols-7 gap-1" wire:loading.remove wire:target="previousMonth,nextMonth,updatedCurrentMonth,updatedResourceId">
            @foreach($this->calendar['weeks'] as $week)
                @foreach($week as $day)
                    <button
                        type="button"
                        wire:click="selectDate('{{ $day['date'] }}')"
                        @class([
                            'relative h-9 rounded-lg text-xs font-medium transition-all',
                            'text-gray-300 dark:text-gray-600' => !$day['inMonth'] || $day['isPast'],
                            'text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer' => $day['inMonth'] && !$day['isPast'] && !$day['isBooked'] && !$day['isPartialBooked'] && !$day['isSelected'],
                            'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 cursor-not-allowed line-through' => $day['isBooked'] && $day['inMonth'] && !$day['isSelected'],
                            'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/30 cursor-pointer' => $day['isPartialBooked'] && $day['inMonth'] && !$day['isSelected'],
                            'bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-semibold' => $day['isSelected'] && !$day['hasConflictWithSelection'] && !$day['isSelectionStart'] && !$day['isSelectionEnd'],
                            'bg-blue-500 dark:bg-blue-600 text-white font-bold rounded-l-lg' => $day['isSelectionStart'] && $day['isSelectionEnd'] === false,
                            'bg-blue-500 dark:bg-blue-600 text-white font-bold rounded-r-lg' => $day['isSelectionEnd'] && $day['isSelectionStart'] === false,
                            'bg-blue-500 dark:bg-blue-600 text-white font-bold' => $day['isSelectionStart'] && $day['isSelectionEnd'],
                            'bg-orange-100 dark:bg-orange-900/40 text-orange-700 dark:text-orange-300 font-semibold ring-1 ring-orange-400' => $day['isSelected'] && $day['hasConflictWithSelection'],
                            'text-blue-500 dark:text-blue-400 font-bold' => $day['isToday'] && !$day['isSelected'],
                        ])
                        @if($day['isPast'] || ($day['isBooked'] && !$day['isSelected'])) disabled @endif
                    >
                        {{ $day['day'] }}
                        @if($day['isBooked'] && $day['inMonth'] && !$day['isSelected'])
                            <span class="absolute bottom-0.5 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-red-400"></span>
                        @endif
                        @if($day['isPartialBooked'] && $day['inMonth'] && !$day['isSelected'])
                            <span class="absolute bottom-0.5 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-amber-400"></span>
                        @endif
                        @if($day['hasConflictWithSelection'])
                            <span class="absolute top-0.5 right-0.5 w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                        @endif
                    </button>
                @endforeach
            @endforeach
        </div>

        {{-- Loading overlay for calendar --}}
        <div class="absolute inset-0 flex items-center justify-center" wire:loading wire:target="previousMonth,nextMonth,updatedCurrentMonth,updatedResourceId">
            <svg class="animate-spin w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-4 mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 flex-wrap">
            @if($type === 'vehicle')
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700"></div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Sudah dibooking</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded bg-blue-500 dark:bg-blue-600"></div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Dipilih ({{ $duration }} hari)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded bg-orange-100 dark:bg-orange-900/40 border border-orange-400"></div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Bentrok</span>
                </div>
            @else
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded bg-amber-50 dark:bg-amber-900/20 border border-amber-300 dark:border-amber-700"></div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Ada jadwal terisi</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded bg-blue-500 dark:bg-blue-600"></div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Dipilih</span>
                </div>
            @endif
        </div>

        {{-- Booked time slots (for room/zoom) --}}
        @if(in_array($type, ['room', 'zoom']) && $selectedDate)
            <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                @if(!empty($bookedSlots))
                    <h5 class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">
                        Jam sudah terisi pada {{ $selectedDate }}:
                    </h5>
                    <div class="flex flex-wrap gap-2">
                        @foreach($bookedSlots as $slot)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs font-medium">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $slot['start'] }} - {{ $slot['end'] }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <h5 class="text-xs font-semibold text-green-600 dark:text-green-400 mb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Tidak ada jadwal terisi pada {{ $selectedDate }}. Sepanjang hari tersedia!
                    </h5>
                @endif
            </div>
        @endif

        {{-- Vehicle conflict warning --}}
        @if($type === 'vehicle' && $selectedDate)
            @if(in_array($selectedDate, $bookedDates) || collect($this->calendar['selectedRange'])->filter(fn($d) => in_array($d, $bookedDates))->isNotEmpty())
                <div class="mt-3 flex items-center gap-2 px-3 py-2 rounded-lg bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400 text-sm font-medium">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Rentang tanggal yang dipilih terdapat tanggal yang sudah dibooking!
                </div>
            @else
                <div class="mt-3 flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-sm font-medium">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Rentang tanggal tersedia!
                </div>
            @endif
        @endif

        {{-- Real-time conflict indicator (for room/zoom) --}}
        @if(in_array($type, ['room', 'zoom']) && $selectedDate)
            <div x-show="conflictStatus !== null" x-cloak class="mt-3">
                <div x-show="conflictStatus === 'available'"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Waktu tersedia! Tidak ada bentrok.
                </div>
                <div x-show="conflictStatus === 'conflict'"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Waktu bentrok dengan jadwal yang sudah ada!
                </div>
            </div>
        @endif
    @endif
</div>
