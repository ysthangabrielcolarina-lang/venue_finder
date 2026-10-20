<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('travel_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barangay_id')->constrained()->cascadeOnDelete();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('minutes');
            $table->unique(['barangay_id', 'venue_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_times');
    }
};