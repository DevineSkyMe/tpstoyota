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
        Schema::table('activity_progress', function (Blueprint $table) {
            $table->foreignId('submitted_by')->nullable()->after('activity_plan_id')
                ->constrained('user_accounts')->nullOnDelete();
            $table->unsignedTinyInteger('progress_pct')->nullable()->after('description');
            $table->string('approval_status', 20)->default('pending')->after('progress_pct');
            $table->text('admin_note')->nullable()->after('approval_status');
            $table->foreignId('reviewed_by')->nullable()->after('admin_note')
                ->constrained('user_accounts')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_progress', function (Blueprint $table) {
            $table->dropConstrainedForeignId('submitted_by');
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['progress_pct', 'approval_status', 'admin_note', 'reviewed_at']);
        });
    }
};
