<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_bookings', function (Blueprint $table) {
            $table->string('guest_ip', 45)->nullable()->after('guest_email');
        });

        Schema::table('zoom_bookings', function (Blueprint $table) {
            $table->string('guest_ip', 45)->nullable()->after('guest_email');
        });

        Schema::table('room_bookings', function (Blueprint $table) {
            $table->string('guest_ip', 45)->nullable()->after('guest_email');
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_bookings', function (Blueprint $table) {
            $table->dropColumn('guest_ip');
        });

        Schema::table('zoom_bookings', function (Blueprint $table) {
            $table->dropColumn('guest_ip');
        });

        Schema::table('room_bookings', function (Blueprint $table) {
            $table->dropColumn('guest_ip');
        });
    }
};
