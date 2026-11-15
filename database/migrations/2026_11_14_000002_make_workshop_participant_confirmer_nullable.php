<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A workshop registration starts as "pending" and has no confirmer yet,
     * so confirmed_by must accept NULL until an Admin approves it.
     */
    public function up(): void
    {
        Schema::table('workshop_participants', function (Blueprint $table) {
            $table->unsignedBigInteger('confirmed_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('workshop_participants', function (Blueprint $table) {
            $table->unsignedBigInteger('confirmed_by')->nullable(false)->change();
        });
    }
};
