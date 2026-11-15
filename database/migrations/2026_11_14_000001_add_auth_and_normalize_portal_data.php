<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Email is required so reports can be delivered to Manager / HRD.
        if (! Schema::hasColumn('user_accounts', 'email')) {
            Schema::table('user_accounts', function (Blueprint $table) {
                $table->string('email', 100)->nullable()->unique()->after('username');
            });
        }

        // 2. Normalize role naming so the new RBAC layer has one source of truth.
        DB::table('user_accounts')->whereIn('role', ['director', 'manager_tps'])->update(['role' => 'manager']);
        DB::table('user_accounts')->where('role', 'tlc')->update(['role' => 'hrd']);
        DB::table('user_accounts')->where('role', 'participant')->update(['role' => 'karyawan']);
        DB::table('user_accounts')->where('role', 'administrator')->update(['role' => 'admin']);

        // 3. Remove orphan test accounts that are not linked to any employee.
        DB::table('user_accounts')
            ->whereNull('employee_id')
            ->where('role', 'karyawan')
            ->where('username', 'like', 'tps_test%')
            ->delete();

        // 4. Give the remaining role accounts a deliverable email address.
        $emails = [
            'admin' => 'admin@tps.local',
            'hrd' => 'hrd@tps.local',
            'omdd' => 'omdd@tps.local',
            'manager' => 'manager@tps.local',
        ];

        foreach ($emails as $role => $email) {
            DB::table('user_accounts')
                ->where('role', $role)
                ->whereNull('email')
                ->orderBy('id')
                ->limit(1)
                ->update(['email' => $email]);
        }

        // 5. Normalize TPS levels: KP4, KP3, KP2, KP1, Advance.
        $levels = [
            'KP 4' => 'KP4', 'KP4' => 'KP4', 'TPS KP 4' => 'KP4', 'TPS KP4' => 'KP4',
            'KP 3' => 'KP3', 'KP3' => 'KP3', 'TPS KP 3' => 'KP3', 'TPS KP3' => 'KP3',
            'KP 2' => 'KP2', 'KP2' => 'KP2', 'TPS KP 2' => 'KP2',
            'KP 1' => 'KP1', 'KP1' => 'KP1', 'TPS KP 1' => 'KP1',
            'TPS Advance' => 'Advance', 'Advance' => 'Advance', 'ADVANCE' => 'Advance',
        ];

        foreach ($levels as $from => $to) {
            if ($from === $to) {
                continue;
            }

            DB::table('employees')->where('current_tps_level', $from)->update(['current_tps_level' => $to]);
            DB::table('employees')->where('previous_tps_level', $from)->update(['previous_tps_level' => $to]);
            DB::table('workshop_candidates')->where('target_tps_level', $from)->update(['target_tps_level' => $to]);
        }

        // 6. Normalize evaluation scores to the 1-5 rubric scale and decision casing.
        DB::table('workshop_evaluations')->orderBy('id')->chunkById(100, function ($evaluations) {
            foreach ($evaluations as $evaluation) {
                $update = [];
                for ($i = 1; $i <= 7; $i++) {
                    $column = 'ws_' . $i;
                    $value = (int) $evaluation->{$column};

                    if ($value > 5) {
                        $value = (int) max(1, min(5, round($value / 20)));
                    }

                    if ($value !== (int) $evaluation->{$column}) {
                        $update[$column] = $value;
                    }
                }

                $decision = strtolower((string) $evaluation->final_decision);
                $decision = in_array($decision, ['pass', 'lulus', 'recommended'], true) ? 'pass' : 'fail';

                if ($decision !== $evaluation->final_decision) {
                    $update['final_decision'] = $decision;
                }

                if ($update !== []) {
                    DB::table('workshop_evaluations')->where('id', $evaluation->id)->update($update);
                }
            }
        });

        // 7. Keep the scheduled workshop in the future if its dates have lapsed.
        DB::table('workshops')
            ->where('status', 'scheduled')
            ->where('end_date', '<', now()->toDateString())
            ->update([
                'start_date' => now()->addWeeks(2)->toDateString(),
                'end_date' => now()->addWeeks(2)->addDays(5)->toDateString(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data normalization is intentionally not reversed.
        if (Schema::hasColumn('user_accounts', 'email')) {
            Schema::table('user_accounts', function (Blueprint $table) {
                $table->dropColumn('email');
            });
        }
    }
};
