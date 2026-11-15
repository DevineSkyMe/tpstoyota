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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('username', 20)->unique(); // NIK
            $table->date('birth_date')->nullable();
            $table->string('position', 100)->nullable();
            $table->string('photo', 500)->nullable();
            $table->string('certificate', 500)->nullable();
            $table->string('current_tps_level', 50)->nullable();
            $table->string('previous_tps_level', 50)->nullable();
            $table->string('tahun_lulus_terakhir', 20)->nullable();
            $table->string('tahun_lulus_saat_ini', 20)->nullable();
            $table->string('last_activity_theme', 200)->nullable();
            $table->string('last_activity_type', 50)->nullable();
            $table->string('batch', 50)->nullable();
            $table->integer('registration_year')->default((int) date('Y'));
            $table->string('status', 20)->default('active');
            $table->foreignId('plant_id')->nullable()->constrained('plants')->nullOnDelete();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

