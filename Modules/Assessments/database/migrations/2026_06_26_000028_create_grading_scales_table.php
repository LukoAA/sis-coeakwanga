<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One configurable grading scale per programme type.
        Schema::create('grading_scales', function (Blueprint $table) {
            $table->id();
            $table->string('programme_type')->unique(); // 'NCE' | 'DEGREE'
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_scales');
    }
};
