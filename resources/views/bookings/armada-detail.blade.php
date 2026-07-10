<x-app-layout title="Detail Booking Armada">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detail Booking Armada
        </h2>
    </x-slot>

    <livewire:bookings.vehicle.vehicle-booking-detail :booking="$booking" />
</x-app-layout>
