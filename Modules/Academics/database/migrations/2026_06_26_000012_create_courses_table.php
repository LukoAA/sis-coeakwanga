<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // NCE and Degree keep SEPARATE course pools, distinguished by programme_type.
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->string('programme_type');   // 'NCE' | 'DEGREE'
            $table->string('code')->unique();
            $table->string('title');
            $table->unsignedSmallInteger('credit_units')->default(2);
            $table->string('course_type');      // 'CORE' | 'ELECTIVE' | 'GES'
            $table->timestamps();

            $table->index('programme_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
