<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Completes the People <-> Academics link. The enrolments table (People)
     * left programme_id / current_level_id / subject_combination_id as nullable
     * placeholders because Academics did not exist yet. Now it does, so we add
     * the real foreign keys.
     *
     * SQLite cannot ALTER an existing table to add foreign keys, and the test
     * suite runs on in-memory SQLite where FK enforcement isn't the point — so
     * we apply the constraints only on real databases (PostgreSQL in production).
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('enrolments', function (Blueprint $table) {
            $table->foreign('programme_id')->references('id')->on('programmes')->nullOnDelete();
            $table->foreign('current_level_id')->references('id')->on('levels')->nullOnDelete();
            $table->foreign('subject_combination_id')->references('id')->on('subject_combinations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('enrolments', function (Blueprint $table) {
            $table->dropForeign(['programme_id']);
            $table->dropForeign(['current_level_id']);
            $table->dropForeign(['subject_combination_id']);
        });
    }
};
