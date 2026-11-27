<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointment_student', function (Blueprint $table) {
            $table->string('attendance_status')
                ->default('not_recorded');

            $table->text('attendance_notes')
                ->nullable();

            $table->timestamp('attendance_marked_at')
                ->nullable();

            $table->foreignId('attendance_marked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('appointment_student', function (Blueprint $table) {
            $table->dropForeign(['attendance_marked_by']);

            $table->dropColumn([
                'attendance_status',
                'attendance_notes',
                'attendance_marked_at',
                'attendance_marked_by',
            ]);
        });
    }
};
