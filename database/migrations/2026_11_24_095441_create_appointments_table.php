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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('course_id')
                ->constrained()
                ->restrictOnDelete();
    
            $table->enum('appointment_type', [
                'individual',
                'group'
            ]);
    
            $table->dateTime('start_at');
            $table->dateTime('end_at');
    
            $table->unsignedSmallInteger('capacity')->nullable();
    
            $table->enum('status', [
                'pending',
                'confirmed',
                'completed',
                'cancelled',
                'no_show'
            ])->default('pending');
    
            $table->string('location')->nullable();
    
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
    
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
    
            $table->foreignId('requested_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
    
            $table->timestamps();
    
            $table->index(['start_at', 'end_at']);
            $table->index(['status', 'start_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
