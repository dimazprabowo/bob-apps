<x-app-layout title="Detail Booking Ruangan">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Booking Ruangan
        </h2>
    </x-slot>

    <livewire:bookings.room.room-booking-detail :booking="$booking" />
</x-app-layout>
