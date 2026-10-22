<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('guest_count');
            $table->string('status')->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamps();

            $table->index(['venue_id', 'start_date', 'end_date']);
        });

        DB::statement("ALTER TABLE reservations ADD CONSTRAINT chk_reservation_status
            CHECK (status IN ('pending','confirmed','rejected','cancelled','expired'))");
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservation_dates
            CHECK (end_date >= start_date)');
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
