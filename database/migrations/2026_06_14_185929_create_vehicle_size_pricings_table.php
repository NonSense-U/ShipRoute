<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_size_pricings', function (Blueprint $table) {
            $table->id();
            $table->enum('size', ['small', 'medium', 'large']);
            $table->decimal('max_capacity_kg', 10, 2);
            $table->decimal('starting_fee', 10, 2);
            $table->decimal('per_km_fee', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_size_pricings');
    }
};
