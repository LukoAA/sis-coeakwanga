<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Audit trail of each approval-stage transition (who, when).
        Schema::create('result_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('score_entry_id')->constrained('score_entries')->cascadeOnDelete();
            $table->string('stage');                // submitted|vetted|approved|published
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_approvals');
    }
};
