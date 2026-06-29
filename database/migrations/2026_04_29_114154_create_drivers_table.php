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
            $table->string('current_governorate')->nullable();
            $table->string('current_lat')->nullable();
            $table->string('current_lon')->nullable();
            $table->timestamp('last_location_at')->nullable();
            $table->enum('vehicle_type', ['open', 'covered', 'refrigerated']);
            $table->enum('vehicle_size', ['small', 'medium', 'large']);
            $table->decimal('vehicle_capacity_kg', 10, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->string('license_plate_number')->unique();
            $table->string('driver_license_number')->unique();
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
