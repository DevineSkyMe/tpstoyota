<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('workshop_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshops')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('candidate_id')->nullable()->constrained('workshop_candidates')->nullOnDelete();
            $table->foreignId('confirmed_by')->constrained('user_accounts')->cascadeOnDelete();
            $table->string('confirmation_status', 20)->default('confirmed');
            $table->timestamps();

            $table->unique(['workshop_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_participants');
    }
};

