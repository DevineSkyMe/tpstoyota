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
        Schema::create('workshop_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('recommended_by')->constrained('user_accounts')->cascadeOnDelete();
            $table->string('target_tps_level', 50)->nullable();
            $table->text('tlc_reason')->nullable();
            $table->string('tlc_status', 20)->default('pending');
            $table->timestamp('tlc_reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_candidates');
    }
};

