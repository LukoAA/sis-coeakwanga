<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A FIXED CATALOG of NCCE-approved double-major pairings.
        // Students pick a combination from here; they don't invent pairings.
        Schema::create('subject_combinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programme_id')->constrained('programmes')->cascadeOnDelete();
            $table->string('name');             // "Mathematics / Integrated Science"
            $table->foreignId('major_subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('minor_subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->timestamps();

            $table->unique(['programme_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_combinations');
    }
};
