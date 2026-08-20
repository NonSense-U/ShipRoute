<?php

use App\Models\Shipment;
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
            $table->foreignIdFor(Shipment::class)->constrained()->cascadeOnDelete();
            $table->text('overview_polyline');
            $table->string('pickup_governorate')->nullable();
            $table->string('pickup_lat');
            $table->string('pickup_lon');
            $table->string('delivery_lat');
            $table->string('delivery_lon');
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
