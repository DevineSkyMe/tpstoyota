<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\UserAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PortalUserSeeder extends Seeder
{
    /**
     * Seed one deterministic login account for every portal role.
     *
     * The seeder is idempotent: it reuses any existing account that already
     * owns the demo username, email, or role instead of inserting duplicates.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        $staff = [
            ['username' => 'admin', 'email' => 'admin@tps.local', 'role' => UserAccount::ROLE_ADMIN],
            ['username' => 'hrd', 'email' => 'hrd@tps.local', 'role' => UserAccount::ROLE_HRD],
            ['username' => 'omdd', 'email' => 'omdd@tps.local', 'role' => UserAccount::ROLE_OMDD],
            ['username' => 'manager', 'email' => 'manager@tps.local', 'role' => UserAccount::ROLE_MANAGER],
        ];

        foreach ($staff as $account) {
            $existing = UserAccount::where('username', $account['username'])->first()
                ?? UserAccount::where('email', $account['email'])->first()
                ?? UserAccount::where('role', $account['role'])->orderBy('id')->first();

            $attributes = [
                'username' => $account['username'],
                'email' => $account['email'],
                'password' => $password,
                'role' => $account['role'],
                'employee_id' => null,
            ];

            $existing
                ? $existing->update($attributes)
                : UserAccount::create($attributes);
        }

        // Karyawan demo account linked to Kenji Takahashi (NIK 51204).
        $employee = Employee::where('username', '51204')->first()
            ?? Employee::orderBy('id')->first();

        if ($employee) {
            $karyawan = UserAccount::where('username', 'karyawan')->first()
                ?? UserAccount::where('employee_id', $employee->id)
                    ->where('role', UserAccount::ROLE_KARYAWAN)
                    ->first();

            $attributes = [
                'username' => 'karyawan',
                'email' => 'karyawan@tps.local',
                'password' => $password,
                'role' => UserAccount::ROLE_KARYAWAN,
                'employee_id' => $employee->id,
            ];

            $karyawan
                ? $karyawan->update($attributes)
                : UserAccount::create($attributes);
        }

        // Make sure every karyawan account still points at a real employee record.
        UserAccount::where('role', UserAccount::ROLE_KARYAWAN)
            ->whereNull('employee_id')
            ->update(['employee_id' => Employee::orderBy('id')->value('id')]);
    }
}
