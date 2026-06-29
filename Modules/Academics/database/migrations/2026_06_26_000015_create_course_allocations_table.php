<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lecturer course allocation / workload, per session and semester.
        Schema::create('course_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->unsignedSmallInteger('semester'); // 1 | 2
            $table->timestamps();

            $table->unique(['course_id', 'academic_session_id', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_allocations');
    }
};
