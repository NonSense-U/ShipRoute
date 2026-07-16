<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_dashboard_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('analytics_month')->unique();
            $table->unsignedInteger('active_drivers_count')->default(0);
            $table->unsignedInteger('inactive_drivers_count')->default(0);
            $table->unsignedInteger('active_merchants_count')->default(0);
            $table->unsignedInteger('inactive_merchants_count')->default(0);
            $table->json('shipments_per_month')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_dashboard_analytics');
    }
};