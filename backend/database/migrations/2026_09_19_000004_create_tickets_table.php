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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->string('ticket_number')->unique();

            $table->string('subject');

            $table->text('description');

            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent',
            ])->default('medium');

            $table->enum('status', [
                'open',
                'in_progress',
                'resolved',
                'closed',
            ])->default('open');

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('target_division_id')
                ->constrained('divisions')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            
            $table->foreignId('assigned_employee_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->timestamp('resolved_at')->nullable();

            $table->timestamp('closed_at')->nullable();

            $table->timestamp('last_user_response_at')
                ->nullable();

            $table->timestamp('last_activity_at')
                ->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('priority');
            $table->index('target_division_id');
            $table->index('assigned_employee_id');
            $table->index('created_by');

            $table->index([
                'status',
                'target_division_id',
            ]);

            $table->index([
                'status',
                'last_user_response_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
