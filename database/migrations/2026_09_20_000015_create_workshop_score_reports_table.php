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
        Schema::create('workshop_score_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshops')->cascadeOnDelete();
            $table->foreignId('generated_by')->constrained('user_accounts')->cascadeOnDelete();
            $table->string('pdf_path', 255);
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamp('sent_to_hrd_at')->nullable();
            $table->timestamp('sent_to_director_at')->nullable();
            $table->string('status', 20)->default('generated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_score_reports');
    }
};

