<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // An invoice belongs to EITHER an enrolment (session fees) OR an
        // application (acceptance fee, before the student exists).
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrolment_id')->nullable()->constrained('enrolments')->cascadeOnDelete();
            $table->foreignId('application_id')->nullable()->constrained('applications')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->decimal('total', 12, 2)->default(0);
            $table->string('status')->default('unpaid'); // unpaid | part | paid
            $table->date('due_on')->nullable();
            $table->timestamps();

            $table->index(['enrolment_id', 'academic_session_id']);
            $table->index('application_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
