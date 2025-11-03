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
        Schema::create('rate_cards', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Descriptive name for this rate card');

            // Scope filters (nullable = applies to all)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');

            // Day of week mask (JSON array of days: [1,2,3,4,5] for Mon-Fri)
            $table->json('days_of_week')->nullable()->comment('Array of day numbers (1=Mon, 7=Sun). Null = all days');

            // Time band
            $table->time('time_start')->nullable()->comment('Start time of band. Null = 00:00');
            $table->time('time_end')->nullable()->comment('End time of band. Null = 23:59:59');

            // Date range
            $table->date('date_start')->nullable()->comment('Effective from date. Null = no start limit');
            $table->date('date_end')->nullable()->comment('Effective until date. Null = no end limit');

            // Rate configuration
            $table->enum('rate_type', ['fixed', 'multiplier'])->default('fixed');
            $table->decimal('rate_amount', 10, 2)->nullable()->comment('Fixed rate amount per hour');
            $table->decimal('rate_multiplier', 5, 2)->nullable()->comment('Multiplier vs base rate (e.g., 1.5 for 150%)');
            $table->string('currency', 3)->default('EUR');

            // Precedence (higher = more specific, wins in conflicts)
            $table->integer('precedence')->default(0)->comment('Higher precedence wins. Auto-calculated based on specificity');

            // Overtime configuration
            $table->boolean('is_overtime')->default(false);
            $table->enum('overtime_type', ['daily', 'weekly'])->nullable();
            $table->decimal('overtime_threshold', 8, 2)->nullable()->comment('Hours threshold (e.g., 8 for daily, 40 for weekly)');

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index(['role_id', 'is_active']);
            $table->index(['project_id', 'is_active']);
            $table->index(['precedence']);
            $table->index(['date_start', 'date_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_cards');
    }
};
