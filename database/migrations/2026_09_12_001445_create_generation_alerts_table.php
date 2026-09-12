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
        Schema::create('generation_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solar_farm_id')->constrained('solar_farms')->cascadeOnDelete();
            $table->foreignId('energy_generation_id')->nullable()->constrained('energy_generations')->cascadeOnDelete();
            $table->string('period', 7);
            $table->decimal('estimated_kwh', 12, 2);
            $table->decimal('real_kwh', 12, 2);
            $table->decimal('deviation_percentage', 5, 2);
            $table->enum('status', ['active', 'resolved'])->default('active');
            $table->text('resolution_notes')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generation_alerts');
    }
};
