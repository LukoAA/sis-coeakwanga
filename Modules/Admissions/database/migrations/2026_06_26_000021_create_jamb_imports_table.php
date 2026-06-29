<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jamb_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->string('file_ref')->nullable();
            $table->string('status')->default('pending'); // pending|processed|failed
            $table->unsignedInteger('rows_total')->default(0);
            $table->unsignedInteger('rows_matched')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jamb_imports');
    }
};
