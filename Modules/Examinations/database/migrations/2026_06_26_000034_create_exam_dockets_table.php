<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One exam docket per student per course per session+semester. The
        // docket is the pass a student presents to sit the exam; it is issued
        // only when the eligibility gate passes, and carries a snapshot of why.
        Schema::create('exam_dockets', function (Blueprint $table) {
            $table->id();
            $table->string('docket_number')->unique();
            $table->foreignId('enrolment_id')->constrained('enrolments')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->unsignedSmallInteger('semester');

            // Eligibility snapshot at issue time (why this docket was granted).
            $table->boolean('registered')->default(false);
            $table->boolean('fee_cleared')->default(false);
            $table->boolean('attendance_ok')->default(false);

            $table->timestamp('issued_at')->nullable();
            $table->timestamps();

            $table->unique(['enrolment_id', 'course_id', 'academic_session_id', 'semester'], 'exam_docket_unique');
            $table->index('docket_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_dockets');
    }
};
