<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The per-programme, per-level, per-semester curriculum. This is what
        // generates a student's course form in the Registration module.
        Schema::create('curriculum_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->constrained('programmes')->cascadeOnDelete();
            $table->foreignId('level_id')->constrained('levels')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->unsignedSmallInteger('semester'); // 1 | 2
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->unique(['programme_id', 'level_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_courses');
    }
};
