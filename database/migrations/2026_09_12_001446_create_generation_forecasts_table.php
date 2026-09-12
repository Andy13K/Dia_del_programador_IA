<?php

declare(strict_types=1);

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
        Schema::create('generation_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solar_farm_id')->constrained('solar_farms')->cascadeOnDelete();
            $table->string('target_period', 7);
            $table->decimal('forecasted_kwh', 12, 2);
            $table->string('method');
            $table->decimal('actual_kwh', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generation_forecasts');
    }
};
