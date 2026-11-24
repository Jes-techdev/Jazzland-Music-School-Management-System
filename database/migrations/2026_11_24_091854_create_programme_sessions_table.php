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
        Schema::create('programme_sessions', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('programme_id')
                ->constrained()
                ->cascadeOnDelete();
    
            $table->foreignId('course_id')
                ->constrained()
                ->restrictOnDelete();
    
            $table->foreignId('instructor_id')
                ->constrained()
                ->restrictOnDelete();
    
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
    
            $table->unsignedSmallInteger('capacity')->nullable();
    
            $table->enum('status', [
                'scheduled',
                'cancelled'
            ])->default('scheduled');
    
            $table->string('location')->nullable();
            $table->timestamps();
    
            $table->index(['instructor_id', 'session_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programme_sessions');
    }
};
