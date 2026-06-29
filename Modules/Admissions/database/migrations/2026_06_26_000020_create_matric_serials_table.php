<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Atomic serial counter, scoped per programme + session, so concurrent
        // admissions can never grab the same matric serial.
        Schema::create('matric_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->constrained('programmes')->cascadeOnDelete();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->unsignedInteger('last_serial')->default(0);
            $table->timestamps();

            $table->unique(['programme_id', 'academic_session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matric_serials');
    }
};
