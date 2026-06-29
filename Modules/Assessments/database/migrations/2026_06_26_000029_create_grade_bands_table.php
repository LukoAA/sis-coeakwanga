<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Score -> grade mapping. Fully configurable; never hard-coded.
        Schema::create('grade_bands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grading_scale_id')->constrained('grading_scales')->cascadeOnDelete();
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->string('grade_letter');         // A, B, C, D, E, F
            $table->decimal('grade_point', 3, 2);   // 5.00, 4.00, ...
            $table->boolean('is_pass')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_bands');
    }
};
