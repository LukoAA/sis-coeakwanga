<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Configurable matric patterns per programme type (and optionally session).
        Schema::create('matric_number_formats', function (Blueprint $table) {
            $table->id();
            $table->string('programme_type');   // 'NCE' | 'DEGREE'
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->nullOnDelete();
            $table->string('pattern');          // COEA/{year}/{school}/{major}/{minor}/{serial}
            $table->unsignedSmallInteger('serial_length')->default(4);
            $table->timestamps();

            $table->unique(['programme_type', 'academic_session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matric_number_formats');
    }
};
