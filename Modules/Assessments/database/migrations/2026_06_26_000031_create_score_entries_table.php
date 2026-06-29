<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrolment_id')->constrained('enrolments')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->unsignedSmallInteger('semester');
            $table->unsignedSmallInteger('credit_units');  // snapshot at scoring time

            $table->decimal('ca_score', 5, 2)->nullable();
            $table->decimal('exam_score', 5, 2)->nullable();
            $table->decimal('total', 5, 2)->nullable();
            $table->string('grade')->nullable();
            $table->decimal('grade_point', 3, 2)->nullable();
            $table->boolean('passed')->nullable();

            // Lifecycle: draft -> submitted -> vetted -> approved -> published
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->unique(['enrolment_id', 'course_id', 'academic_session_id', 'semester'], 'score_entry_unique');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('score_entries');
    }
};
