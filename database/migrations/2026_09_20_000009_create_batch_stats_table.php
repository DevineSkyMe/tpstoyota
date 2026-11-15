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
        Schema::create('batch_stats', function (Blueprint $table) {
            $table->id();
            $table->string('batch_name', 50);
            $table->integer('participant_count')->default(0);
            $table->integer('kp3_count')->default(0);
            $table->integer('kp4_count')->default(0);
            $table->string('kp3_percent', 20)->nullable();
            $table->string('kp4_percent', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_stats');
    }
};

