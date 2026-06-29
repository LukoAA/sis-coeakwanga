<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrolments', function (Blueprint $table) {
            $table->id();

            // The human this admission belongs to. One person -> many enrolments.
            $table->foreignId('person_id')
                ->constrained('people')
                ->cascadeOnDelete();

            // Identifies the admission. Matric lives HERE, not on people.
            $table->string('matric_number')->unique();
            $table->string('programme_type');       // 'NCE' | 'DEGREE' — drives the level scheme
            $table->string('entry_route');          // 'UTME' | 'DIRECT_ENTRY'
            $table->string('status')->default('active'); // active|graduated|withdrawn|deferred
            $table->string('graduation_outcome')->nullable();
            $table->date('graduated_at')->nullable();

            // Admission session lives in Identity (shared kernel) — real FK.
            $table->foreignId('admission_session_id')
                ->nullable()
                ->constrained('academic_sessions')
                ->nullOnDelete();

            // These reference the Academics module, which is not built yet.
            // Kept nullable + indexed now; foreign keys added when Academics lands.
            $table->unsignedBigInteger('programme_id')->nullable()->index();
            $table->unsignedBigInteger('current_level_id')->nullable()->index();
            $table->unsignedBigInteger('subject_combination_id')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();

            $table->index('person_id');
            $table->index('programme_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrolments');
    }
};
