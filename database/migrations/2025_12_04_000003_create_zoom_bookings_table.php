<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zoom_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            // Guest fields (hybrid)
            $table->string('guest_name')->nullable();
            $table->string('guest_phone', 20)->nullable();
            $table->string('guest_divisi')->nullable();
            $table->string('guest_email')->nullable();

            // Booking details
            $table->string('topic');
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('platform')->default('Zoom'); // Zoom, Google Meet, Microsoft Teams
            $table->string('meeting_link')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('booking_date');
            $table->index('booking_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zoom_bookings');
    }
};
