<?php

use App\Models\ShipmentRoute;
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
        Schema::create('checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(ShipmentRoute::class)->constrained()->onDelete('cascade');
            $table->enum('type', ['pick_up', 'delivery']);
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_phone_number')->nullable();
            $table->string('address')->nullable();
            $table->string('street')->nullable();
            $table->string('building_number')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkpoints');
    }
};
