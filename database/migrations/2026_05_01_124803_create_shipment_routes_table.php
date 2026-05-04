<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipment_routes', function (Blueprint $table) {
            $table->id();
            $table->string('overview_polyline');
            $table->json('pick_up_location_details')->nullable();
            $table->json('delivery_location_details')->nullable();
            $table->string('pick_up_lat');
            $table->string('pick_up_lng');
            $table->string('delivery_lat');
            $table->string('delivery_lng');
            $table->decimal('distance', 10, 2);
            $table->unsignedInteger('duration_minutes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_routes');
    }
};
