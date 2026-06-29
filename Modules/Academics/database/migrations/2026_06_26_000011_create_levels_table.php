<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Programme-type-aware level schemes (ADR-0001). NCE {NCE1,NCE2,NCE3}
        // and Degree {300,400} live here as separate, non-colliding schemes.
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('programme_type');   // 'NCE' | 'DEGREE'
            $table->string('code');             // 'NCE1','NCE2','NCE3' | '300','400'
            $table->string('label');            // 'NCE 1' | '300 Level'
            $table->unsignedSmallInteger('rank'); // ordering within the scheme
            $table->timestamps();

            $table->unique(['programme_type', 'code']);
            $table->index('programme_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
