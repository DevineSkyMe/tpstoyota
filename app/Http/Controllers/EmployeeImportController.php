<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Plant;
use App\Models\UserAccount;
use App\Support\XlsxReader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class EmployeeImportController extends Controller
{
    /**
     * Header aliases (normalised) mapped to the internal field name.
     *
     * @var array<string, array<int, string>>
     */
    private const HEADER_ALIASES = [
        'noreg' => ['noreg', 'no_reg', 'nik', 'no_induk', 'nomor_induk'],
        'nama' => ['nama', 'name', 'nama_lengkap', 'nama_pegawai'],
        'foto' => ['foto', 'photo', 'url_foto'],
        'jabatan' => ['jabatan', 'position', 'posisi'],
        'batch' => ['batch', 'angkatan'],
        'divisi' => ['divisi', 'division', 'div'],
        'departemen' => ['departemen', 'department', 'dept'],
        'plant' => ['plant_id', 'plant', 'pabrik'],
        'level' => ['kp_id', 'level', 'tps_level', 'level_tps', 'key_person'],
        'tahun_lahir' => ['tahun_lahir', 'birth_year', 'thn_lahir'],
        'tahun_pensiun' => ['tahun_pensiun', 'retirement_year', 'thn_pensiun'],
        'proses' => ['proses_id', 'proses', 'process', 'process_id', 'proses_produksi'],
    ];

    /**
     * Show the upload form and the last import summary (if any).
     */
    public function create(): View
    {
        return view('employees.import', [
            'levels' => array_reverse(Employee::TPS_LEVELS),
            'defaultPassword' => 'password',
        ]);
    }

    /**
     * Parse the uploaded workbook, upsert employees and create login accounts.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'default_password' => ['required', 'string', 'min:4', 'max:50'],
            'fallback_level' => ['nullable', 'string', 'in:Advance,KP1,KP2,KP3,KP4'],
        ]);

        $path = $request->file('file')->getRealPath();

        try {
            $sheets = (new XlsxReader())->sheets($path);
        } catch (Throwable $exception) {
            return back()->withInput()->with('error', 'File tidak dapat dibaca: ' . $exception->getMessage());
        }

        $summary = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'accounts' => 0,
            'sheets' => [],
            'errors' => [],
        ];

        $password = (string) $validated['default_password'];
        $fallbackLevel = $validated['fallback_level'] ?? 'Advance';
        $updateExisting = $request->boolean('update_existing');

        foreach ($sheets as $sheetName => $rows) {
            $this->importSheet($sheetName, $rows, $password, $fallbackLevel, $updateExisting, $summary);
        }

        if ($summary['sheets'] === []) {
            return back()->withInput()->with('error', 'Tidak ada data pegawai yang dikenali. Pastikan file berisi kolom NOREG dan NAMA.');
        }

        session()->flash('import_result', $summary);

        $message = "Import selesai: {$summary['created']} pegawai baru, {$summary['updated']} diperbarui, "
            . "{$summary['accounts']} akun login dibuat";

        if ($summary['skipped'] > 0) {
            $message .= ", {$summary['skipped']} dilewati (NIK sudah ada)";
        }

        return redirect()->route('employees.import')->with('success', $message . '.');
    }

    /**
     * Import a single worksheet. Sheets without NOREG/NAMA headers are ignored.
     *
     * @param  array<int, array<int, string>>  $rows
     * @param  array<string, mixed>  $summary
     */
    private function importSheet(
        string $sheetName,
        array $rows,
        string $password,
        string $fallbackLevel,
        bool $updateExisting,
        array &$summary
    ): void {
        $headerIndex = null;
        $map = [];

        foreach ($rows as $index => $row) {
            $candidate = $this->mapHeader($row);

            if (isset($candidate['noreg'], $candidate['nama'])) {
                $headerIndex = $index;
                $map = $candidate;
                break;
            }
        }

        if ($headerIndex === null) {
            return;
        }

        $stats = ['rows' => 0, 'created' => 0, 'updated' => 0, 'skipped' => 0];
        $sheetLevel = $this->sheetLevel($sheetName);

        for ($i = $headerIndex + 1, $count = count($rows); $i < $count; $i++) {
            $row = $rows[$i];
            $noreg = $this->cell($row, $map['noreg'] ?? null);
            $name = $this->cell($row, $map['nama'] ?? null);

            if ($noreg === '' || $name === '') {
                continue;
            }

            $stats['rows']++;

            try {
                $result = $this->upsertEmployee($row, $map, $noreg, $name, $sheetLevel, $fallbackLevel, $password, $updateExisting);

                $stats[$result['status']]++;
                $summary[$result['status']]++;

                if ($result['account_created']) {
                    $summary['accounts']++;
                }
            } catch (Throwable $exception) {
                $summary['errors'][] = sprintf(
                    'Sheet "%s" baris %d (NIK %s): %s',
                    $sheetName,
                    $i + 1,
                    $noreg,
                    $exception->getMessage()
                );
            }
        }

        $summary['sheets'][$sheetName] = $stats;
    }

    /**
     * Create or update one employee and make sure they have a login account.
     *
     * `$sheetLevel` is an authoritative level implied by the worksheet name
     * (e.g. the TPS ADVANCE sheet). `$fallbackLevel` is only used to seed the
     * level of brand new employees when neither the row nor the sheet states one.
     *
     * @param  array<int, string>  $row
     * @param  array<string, int>  $map
     * @return array{status: string, account_created: bool}
     */
    private function upsertEmployee(
        array $row,
        array $map,
        string $noreg,
        string $name,
        ?string $sheetLevel,
        string $fallbackLevel,
        string $password,
        bool $updateExisting
    ): array {
        $rowLevel = isset($map['level'])
            ? $this->normalizeLevel($this->cell($row, $map['level']))
            : null;

        // A level written in the sheet always wins over the generic fallback.
        $explicitLevel = $rowLevel ?? $sheetLevel;

        $data = array_filter([
            'name' => $name,
            'photo' => $this->nullIfEmpty($this->cell($row, $map['foto'] ?? null)),
            'position' => $this->nullIfEmpty($this->cell($row, $map['jabatan'] ?? null)),
            'batch' => $this->normalizeBatch($this->cell($row, $map['batch'] ?? null)),
            'birth_date' => $this->normalizeBirthDate($this->cell($row, $map['tahun_lahir'] ?? null)),
            'retirement_year' => $this->normalizeYear($this->cell($row, $map['tahun_pensiun'] ?? null)),
            'process_stage' => $this->nullIfEmpty($this->cell($row, $map['proses'] ?? null)),
            'plant_id' => $this->referenceId(Plant::class, $this->cell($row, $map['plant'] ?? null)),
            'division_id' => $this->referenceId(Division::class, $this->cell($row, $map['divisi'] ?? null)),
            'department_id' => $this->referenceId(Department::class, $this->cell($row, $map['departemen'] ?? null)),
        ], fn ($value) => $value !== null);

        $employee = Employee::where('username', $noreg)->first();

        if ($employee !== null) {
            if (! $updateExisting) {
                return ['status' => 'skipped', 'account_created' => false];
            }

            $employee->fill($data);

            // Never downgrade an existing level just because a sheet omits it.
            if ($explicitLevel !== null) {
                $employee->current_tps_level = $explicitLevel;
            }

            $employee->save();

            return [
                'status' => 'updated',
                'account_created' => $this->ensureAccount($employee, $noreg, $password),
            ];
        }

        $data['username'] = $noreg;
        $data['current_tps_level'] = $explicitLevel ?? $fallbackLevel;
        $data['status'] = 'active';

        $employee = Employee::create($data);

        return [
            'status' => 'created',
            'account_created' => $this->ensureAccount($employee, $noreg, $password),
        ];
    }

    /**
     * Give the employee a karyawan login account with the default password.
     */
    private function ensureAccount(Employee $employee, string $noreg, string $password): bool
    {
        if (UserAccount::where('employee_id', $employee->id)->exists()) {
            return false;
        }

        UserAccount::create([
            'username' => $this->uniqueAccountUsername($noreg),
            'email' => $this->uniqueAccountEmail($noreg),
            'password' => $password,
            'role' => UserAccount::ROLE_KARYAWAN,
            'employee_id' => $employee->id,
        ]);

        return true;
    }

    /**
     * @param  array<int, string>  $row
     * @return array<string, int>
     */
    private function mapHeader(array $row): array
    {
        $map = [];

        foreach ($row as $index => $value) {
            $key = strtoupper(trim((string) $value));
            $key = str_replace([' ', '-', '.', '/'], '_', $key);
            $key = trim($key, '_');

            if ($key === '') {
                continue;
            }

            foreach (self::HEADER_ALIASES as $field => $aliases) {
                if (in_array(strtolower($key), $aliases, true)) {
                    $map[$field] = $index;
                    break;
                }
            }
        }

        return $map;
    }

    /**
     * Level implied by the worksheet name itself. The TPS ADVANCE sheet is
     * authoritative, so every row in it is treated as Advance. Other sheets
     * return null and let the row/fallback decide.
     */
    private function sheetLevel(string $sheetName): ?string
    {
        if (str_contains(strtoupper($sheetName), 'ADVANCE')) {
            return 'Advance';
        }

        return null;
    }

    private function normalizeLevel(string $raw): ?string
    {
        $value = strtoupper(str_replace([' ', '-', '_'], '', trim($raw)));

        if ($value === '') {
            return null;
        }

        if (str_contains($value, 'ADVANCE')) {
            return 'Advance';
        }

        if (preg_match('/KP0*([1-4])/', $value, $matches) === 1) {
            return 'KP' . $matches[1];
        }

        return null;
    }

    private function normalizeBatch(string $raw): ?string
    {
        $digits = preg_replace('/[^0-9]/', '', $raw);

        if ($digits === null || $digits === '') {
            return null;
        }

        return (string) (int) $digits;
    }

    private function normalizeBirthDate(string $raw): ?string
    {
        $year = $this->normalizeYear($raw);

        return $year === null ? null : sprintf('%04d-01-01', $year);
    }

    private function normalizeYear(string $raw): ?int
    {
        $digits = preg_replace('/[^0-9]/', '', $raw);

        if ($digits === null || strlen($digits) !== 4) {
            return null;
        }

        $year = (int) $digits;

        return ($year >= 1900 && $year <= 2100) ? $year : null;
    }

    /**
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     */
    private function referenceId(string $model, string $name): ?int
    {
        $name = trim($name);

        if ($name === '') {
            return null;
        }

        return $model::firstOrCreate(['name' => $name])->id;
    }

    /**
     * @param  array<int, string>  $row
     */
    private function cell(array $row, ?int $index): string
    {
        if ($index === null) {
            return '';
        }

        return trim((string) ($row[$index] ?? ''));
    }

    private function nullIfEmpty(string $value): ?string
    {
        return $value === '' ? null : $value;
    }

    private function uniqueAccountUsername(string $noreg): string
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $noreg));
        $base = 'tps_' . ($slug !== '' ? $slug : 'user');
        $username = $base;
        $suffix = 1;

        while (UserAccount::where('username', $username)->exists()) {
            $username = $base . '_' . $suffix++;
        }

        return $username;
    }

    private function uniqueAccountEmail(string $noreg): string
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9._-]/', '', $noreg));
        $local = $slug !== '' ? $slug : 'user';
        $email = $local . '@tps.local';
        $suffix = 1;

        while (UserAccount::where('email', $email)->exists()) {
            $email = $local . '_' . $suffix++ . '@tps.local';
        }

        return $email;
    }
}
