<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();

            // Required core — the irreducible identity + match signals.
            $table->string('surname');
            $table->string('first_name');
            $table->string('gender');               // 'male' | 'female'
            $table->date('date_of_birth');
            $table->string('phone');

            // Optional — captured later (clearance / registration).
            $table->string('other_names')->nullable();
            $table->string('email')->nullable();
            $table->string('state_of_origin')->nullable();
            $table->string('lga')->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->string('next_of_kin_relationship')->nullable();

            $table->timestamps();
            $table->softDeletes();                  // removing a student preserves history

            // Match signals — indexed for the PeopleDirectory matcher.
            $table->index('phone');
            $table->index('date_of_birth');
            $table->index('surname');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
