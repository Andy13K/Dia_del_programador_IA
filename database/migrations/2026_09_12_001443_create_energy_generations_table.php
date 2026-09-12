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
        Schema::create('energy_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solar_farm_id')->constrained('solar_farms')->cascadeOnDelete();
            $table->string('period', 7);
            $table->date('record_date');
            $table->decimal('estimated_kwh', 12, 2);
            $table->decimal('real_kwh', 12, 2);
            $table->decimal('co2_kg', 12, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['solar_farm_id', 'period']);
            $table->index('period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('energy_generations');
    }
};
