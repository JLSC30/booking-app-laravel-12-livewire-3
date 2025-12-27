<?php

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
        Schema::create('branch_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('day_of_week')->unsigned(); // 1 = Monday, 7 = Sunday
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('slot_interval_minutes')->default(30); // e.g., 30 or 60 min steps
            $table->boolean('is_available')->default(true); // false = full day off
            $table->timestamps();

            $table->unique(['branch_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_schedules');
    }
};
