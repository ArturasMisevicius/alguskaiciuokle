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
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->integer('break_duration')->default(0)->comment('Break duration in minutes');
            $table->text('note')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');

            // Live timer fields
            $table->timestamp('timer_started_at')->nullable();
            $table->boolean('timer_running')->default(false);

            // Pricing fields (will be calculated and stored)
            $table->decimal('calculated_hours', 8, 2)->nullable()->comment('Total hours (minus break)');
            $table->decimal('total_amount', 10, 2)->nullable()->comment('Total calculated amount');
            $table->string('currency', 3)->default('EUR');

            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'date']);
            $table->index(['status']);
            $table->index(['timer_running']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timesheets');
    }
};
