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
        Schema::create('workshop_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('workshop_id')->nullable()->constrained('workshops')->nullOnDelete();
            $table->foreignId('participant_id')->nullable()->constrained('workshop_participants')->nullOnDelete();
            $table->integer('ws_1')->default(0);
            $table->integer('ws_2')->default(0);
            $table->integer('ws_3')->default(0);
            $table->integer('ws_4')->default(0);
            $table->integer('ws_5')->default(0);
            $table->integer('ws_6')->default(0);
            $table->integer('ws_7')->default(0);
            $table->string('final_decision', 20)->default('PASS');
            $table->text('notes')->nullable();
            $table->string('evaluated_by', 100)->nullable();
            $table->foreignId('evaluated_by_id')->nullable()->constrained('user_accounts')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_evaluations');
    }
};

