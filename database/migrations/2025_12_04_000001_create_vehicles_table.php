<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('plate_number')->unique();
            $table->enum('category', ['operasional_harian', 'project', 'operasional_struktural'])->default('operasional_harian');
            $table->enum('status', ['tersedia', 'dipakai', 'maintenance', 'tidak_tersedia'])->default('tersedia');
            $table->date('contract_date')->nullable();
            $table->date('contract_expiry')->nullable();
            $table->string('contract_company')->nullable();
            $table->date('tax_expiry')->nullable();
            $table->date('stnk_expiry')->nullable();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('category');
            $table->index('tax_expiry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
