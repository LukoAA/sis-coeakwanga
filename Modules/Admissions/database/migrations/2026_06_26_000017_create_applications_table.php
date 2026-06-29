<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            // Null until the applicant is matched to / created as a Person.
            $table->foreignId('person_id')->nullable()->constrained('people')->nullOnDelete();

            $table->foreignId('programme_id')->constrained('programmes')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('subject_combination_id')->nullable()->constrained('subject_combinations')->nullOnDelete();

            $table->string('entry_route');          // 'UTME' | 'DIRECT_ENTRY'
            $table->string('jamb_reg_no')->nullable();
            $table->string('applicant_nce_matric')->nullable(); // prior NCE matric for Direct Entry match
            $table->decimal('screening_score', 6, 2)->nullable();
            $table->string('status')->default('pending'); // pending|screened|offered|accepted|rejected|enrolled
            $table->boolean('acceptance_fee_paid')->default(false);

            // Applicant biodata captured before a Person exists (powers matching).
            $table->string('applicant_surname');
            $table->string('applicant_first_name');
            $table->string('applicant_other_names')->nullable();
            $table->string('applicant_gender')->nullable();
            $table->date('applicant_dob')->nullable();
            $table->string('applicant_phone')->nullable();
            $table->string('applicant_email')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('entry_route');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
