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
            $table->foreignIdFor(Driver::class)->constrained()->nullOnDelete();
            $table->foreignIdFor(ShipmentRoute::class);
            $table->decimal('weight');
            $table->decimal('price');
            $table->enum('status', ['']);
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
