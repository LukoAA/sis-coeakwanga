<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrolment_id')->constrained('enrolments')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->unsignedSmallInteger('semester');   // 1 | 2 (aligns with curriculum_courses)
            $table->foreignId('level_id')->constrained('levels')->cascadeOnDelete();
            $table->string('status')->default('draft'); // draft | submitted | approved
            $table->boolean('fee_cleared')->default(false); // snapshot at submit
            $table->unsignedSmallInteger('total_units')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['enrolment_id', 'academic_session_id', 'semester']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
