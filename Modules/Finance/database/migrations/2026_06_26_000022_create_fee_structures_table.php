<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Configurable fees. Match on session + programme_type, optionally
        // narrowed to a specific programme and/or level.
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // "Tuition", "Acceptance Fee", "Library"
            $table->string('fee_type');             // 'ACCEPTANCE' | 'TUITION' | 'SUNDRY'
            $table->string('programme_type');       // 'NCE' | 'DEGREE'
            $table->foreignId('academic_session_id')->constrained('academic_sessions')->cascadeOnDelete();
            $table->foreignId('programme_id')->nullable()->constrained('programmes')->nullOnDelete();
            $table->foreignId('level_id')->nullable()->constrained('levels')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->index(['academic_session_id', 'programme_type', 'fee_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};
