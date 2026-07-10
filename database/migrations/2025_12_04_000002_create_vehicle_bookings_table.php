<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            // Guest fields (hybrid — nullable when user is authenticated)
            $table->string('guest_name')->nullable();
            $table->string('guest_phone', 20)->nullable();
            $table->string('guest_divisi')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_ip', 45)->nullable();

            // Booking details
            $table->date('booking_date');
            $table->unsignedSmallInteger('duration')->default(1); // days
            $table->string('destination');
            $table->string('driver')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->index('vehicle_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('booking_date');
            $table->index('booking_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_bookings');
    }
};
