<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Computed GPA/CGPA per enrolment per session+semester.
        Schema::create('result_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrolment_id')->constrained('enrolments')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->unsignedSmallInteger('semester');
            $table->unsignedInteger('tcu')->default(0);     // total credit units
            $table->decimal('tgp', 8, 2)->default(0);       // total grade points
            $table->decimal('gpa', 3, 2)->default(0);
            $table->decimal('cgpa', 3, 2)->default(0);
            $table->string('classification')->nullable();
            $table->timestamps();

            $table->unique(['enrolment_id', 'academic_session_id', 'semester'], 'result_summary_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_summaries');
    }
};
