<?php

use App\Models\Driver;
use App\Models\Merchant;
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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Merchant::class)->constrained()->nullOnDelete();
            $table->foreignIdFor(Driver::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(ShipmentRoute::class)->constrained()->cascadeOnDelete();
            $table->string('goods_type');
            $table->enum('who_pays', ['sender', 'receiver']);
            $table->string('vehicle_type');
            $table->string('vehicle_capacity_kg');
            $table->decimal('weight', 10, 2);
            $table->text('additional_details')->nullable();
            $table->timestamp('scheduled_pickup_at')->nullable();
            $table->boolean('is_night_shipping')->default(false);
            $table->decimal('price', 10, 2);
            $table->enum('status', [
                'created',
                'accepted',
                'heading_to_pickup',
                'in_transit',
                'delivered',
                'cancelled',
                'expired'
            ])->default('created');
            $table->json('media')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
