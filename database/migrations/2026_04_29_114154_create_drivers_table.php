<?php

use App\Models\User;
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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->string('current_lat')->nullable();
            $table->string('current_lng')->nullable();
            $table->string('vehicle_type');
            $table->integer('vehicle_capacity_kg')->nullable();
            $table->string('license_plate_number')->nullable()->unique();
            $table->string('driver_license_number')->nullable()->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
