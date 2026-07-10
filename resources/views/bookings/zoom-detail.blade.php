<x-app-layout title="Detail Booking Meeting Online">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Booking Meeting Online
        </h2>
    </x-slot>

    <livewire:bookings.zoom.zoom-booking-detail :booking="$booking" />
</x-app-layout>
