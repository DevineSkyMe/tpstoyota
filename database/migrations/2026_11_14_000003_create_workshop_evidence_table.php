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
        Schema::create('workshop_evidences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained('workshop_participants')->cascadeOnDelete();
            $table->unsignedInteger('week_no');
            $table->string('title', 150)->nullable();
            $table->text('note')->nullable();
            $table->string('file_path')->nullable();
            $table->string('link_url')->nullable();
            $table->string('status', 20)->default('submitted'); // submitted | approved | rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('user_accounts')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['participant_id', 'week_no']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_evidences');
    }
};
