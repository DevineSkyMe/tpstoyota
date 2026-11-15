<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extra profile columns that the HRD Excel export carries
     * (TAHUN PENSIUN and the Advance PROSES_ID stage).
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'retirement_year')) {
                $table->unsignedSmallInteger('retirement_year')->nullable()->after('registration_year');
            }

            if (! Schema::hasColumn('employees', 'process_stage')) {
                $table->string('process_stage', 100)->nullable()->after('retirement_year');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['retirement_year', 'process_stage']);
        });
    }
};
