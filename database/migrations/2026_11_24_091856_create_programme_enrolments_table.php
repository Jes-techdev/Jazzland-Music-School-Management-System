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
        Schema::create('programme_enrolments', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('programme_id')
                ->constrained()
                ->cascadeOnDelete();
    
            $table->foreignId('student_id')
                ->constrained()
                ->restrictOnDelete();
    
            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled',
                'completed'
            ])->default('pending');
    
            $table->timestamp('enrolled_at')->nullable();
            $table->text('notes')->nullable();
    
            $table->timestamps();
    
            $table->unique(['programme_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programme_enrolments');
    }
};
