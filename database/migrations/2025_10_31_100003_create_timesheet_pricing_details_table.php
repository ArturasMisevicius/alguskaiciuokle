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
        Schema::create('timesheet_pricing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('timesheet_id')->constrained()->onDelete('cascade');
            $table->foreignId('rate_card_id')->nullable()->constrained()->onDelete('set null');

            // Time segment details
            $table->date('segment_date');
            $table->time('segment_start');
            $table->time('segment_end');
            $table->decimal('segment_hours', 8, 2)->comment('Hours for this segment');

            // Applied rate
            $table->enum('rate_type', ['fixed', 'multiplier'])->default('fixed');
            $table->decimal('applied_rate', 10, 2)->comment('Rate applied for this segment');
            $table->decimal('segment_amount', 10, 2)->comment('Calculated amount for this segment');
            $table->string('currency', 3)->default('EUR');

            // Overtime flag
            $table->boolean('is_overtime')->default(false);
            $table->string('overtime_type')->nullable()->comment('daily or weekly');

            // Metadata
            $table->json('calculation_metadata')->nullable()->comment('Additional calculation details');

            $table->timestamps();

            // Indexes
            $table->index(['timesheet_id']);
            $table->index(['segment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timesheet_pricing_details');
    }
};
