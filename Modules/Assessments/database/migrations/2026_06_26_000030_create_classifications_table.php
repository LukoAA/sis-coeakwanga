<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // CGPA -> class label. NCE (Distinction/Upper Credit/...) and Degree
        // (First Class/2:1/...) both configurable per programme type.
        Schema::create('classifications', function (Blueprint $table) {
            $table->id();
            $table->string('programme_type');       // 'NCE' | 'DEGREE'
            $table->decimal('min_cgpa', 3, 2);
            $table->decimal('max_cgpa', 3, 2);
            $table->string('label');                // 'First Class', 'Distinction', ...
            $table->timestamps();

            $table->index('programme_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classifications');
    }
};
