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
        Schema::create('instructor_instrument', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('instructor_id')
                ->constrained()
                ->cascadeOnDelete();
    
            $table->foreignId('instrument_id')
                ->constrained()
                ->cascadeOnDelete();
    
            $table->timestamps();
    
            $table->unique(['instructor_id', 'instrument_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_instrument');
    }
};
