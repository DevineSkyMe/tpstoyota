<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\UserAccount;
use App\Support\XlsxReader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use ZipArchive;

/**
 * Covers the Excel bulk-import flow. Runs inside a transaction so the shared
 * development database is left untouched.
 */
class EmployeeImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_admin_imports_workbook_and_accounts_are_created(): void
    {
        $this->actingAs(UserAccount::where('role', UserAccount::ROLE_ADMIN)->orderBy('id')->firstOrFail());

        $file = $this->uploadedWorkbook([
            'TPS KP' => [
                ['NOREG', 'NAMA', 'FOTO', 'JABATAN', 'BATCH', 'DIVISI', 'DEPARTEMEN', 'PLANT_ID', 'KP_ID', 'TAHUN LAHIR', 'TAHUN PENSIUN'],
                ['99000001', 'IMPORT TEST SATU', 'https://example.test/1.jpg', 'SECTION HEAD', '#01', 'PWDD', 'Body Production & Insp P#1 Dept', 'KARAWANG 1', 'TPS KP 4', '1974', '2029'],
                ['99000002', 'IMPORT TEST DUA', '', 'GROUP LEADER', '#02', 'APPD', 'Assy Production #1 & PIO Dept', 'KARAWANG 2', 'TPS KP 3', '1980', '2035'],
            ],
            'Pass Percentage' => [
                ['', 'BATCH', 'CANDIDATE', 'PASSED'],
                ['', '#1', '5', '4'],
            ],
        ]);

        $response = $this->post(route('employees.import.store'), [
            'file' => $file,
            'default_password' => 'rahasia123',
            'fallback_level' => 'Advance',
            'update_existing' => '1',
        ]);

        $response->assertRedirect(route('employees.import'));
        $response->assertSessionHasNoErrors();

        $one = Employee::where('username', '99000001')->first();
        $this->assertNotNull($one);
        $this->assertSame('IMPORT TEST SATU', $one->name);
        $this->assertSame('KP4', $one->current_tps_level);
        $this->assertSame('1', $one->batch);
        $this->assertSame(2029, $one->retirement_year);
        $this->assertSame('1974-01-01', $one->birth_date->format('Y-m-d'));
        $this->assertNotNull($one->plant_id);
        $this->assertNotNull($one->division_id);
        $this->assertNotNull($one->department_id);

        $two = Employee::where('username', '99000002')->first();
        $this->assertNotNull($two);
        $this->assertSame('KP3', $two->current_tps_level);

        // The "Pass Percentage" sheet has no NOREG/NAMA and must be ignored.
        $this->assertDatabaseMissing('employees', ['username' => '#1']);

        $account = UserAccount::where('employee_id', $one->id)->first();
        $this->assertNotNull($account);
        $this->assertSame('tps_99000001', $account->username);
        $this->assertSame('99000001@tps.local', $account->email);
        $this->assertSame(UserAccount::ROLE_KARYAWAN, $account->role);
        $this->assertTrue(Hash::check('rahasia123', $account->password));
    }

    public function test_level_less_sheet_uses_fallback_level(): void
    {
        $this->actingAs(UserAccount::where('role', UserAccount::ROLE_ADMIN)->orderBy('id')->firstOrFail());

        $file = $this->uploadedWorkbook([
            'MEMBER OFFICE JISHUKEN' => [
                ['NOREG', 'NAMA', 'FOTO', 'JABATAN', 'BATCH', 'DIVISI', 'DEPARTEMEN', 'PLANT_ID'],
                ['99000010', 'IMPORT OFFICE SATU', '', 'STAFF', '#03', 'PAD', 'PPIC & Warehouse Dept', 'KARAWANG 1'],
            ],
        ]);

        $this->post(route('employees.import.store'), [
            'file' => $file,
            'default_password' => 'password',
            'fallback_level' => 'KP2',
        ])->assertSessionHasNoErrors();

        $this->assertSame('KP2', Employee::where('username', '99000010')->value('current_tps_level'));
    }

    public function test_reimport_skips_existing_when_update_disabled(): void
    {
        $this->actingAs(UserAccount::where('role', UserAccount::ROLE_ADMIN)->orderBy('id')->firstOrFail());

        $employee = Employee::create([
            'name' => 'Nama Lama',
            'username' => '99000020',
            'current_tps_level' => 'Advance',
            'status' => 'active',
        ]);

        $file = $this->uploadedWorkbook([
            'TPS KP' => [
                ['NOREG', 'NAMA', 'KP_ID'],
                ['99000020', 'Nama Baru', 'TPS KP 4'],
            ],
        ]);

        $response = $this->post(route('employees.import.store'), [
            'file' => $file,
            'default_password' => 'password',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('Nama Lama', $employee->fresh()->name);
        $this->assertSame('Advance', $employee->fresh()->current_tps_level);
        $this->assertDatabaseMissing('employees', ['username' => '99000020', 'name' => 'Nama Baru']);
    }

    public function test_non_admin_cannot_open_or_run_the_import(): void
    {
        $this->actingAs(UserAccount::where('role', UserAccount::ROLE_KARYAWAN)->orderBy('id')->firstOrFail());

        $this->get(route('employees.import'))->assertForbidden();
        $this->post(route('employees.import.store'), [])->assertForbidden();
    }

    public function test_pure_php_reader_parses_workbook_without_zip_extension(): void
    {
        $file = $this->uploadedWorkbook([
            'TPS KP' => [
                ['NOREG', 'NAMA', 'KP_ID'],
                ['99000030', 'PURE READER TEST', 'TPS KP 4'],
            ],
        ]);

        $sheets = (new XlsxReader(preferNative: false))->sheets($file->getRealPath());

        $this->assertArrayHasKey('TPS KP', $sheets);
        $this->assertSame(['NOREG', 'NAMA', 'KP_ID'], $sheets['TPS KP'][0]);
        $this->assertSame('99000030', $sheets['TPS KP'][1][0]);
        $this->assertSame('PURE READER TEST', $sheets['TPS KP'][1][1]);
        $this->assertSame('TPS KP 4', $sheets['TPS KP'][1][2]);
    }

    public function test_pure_php_reader_handles_shared_string_table(): void
    {
        $file = $this->uploadedWorkbook([
            'TPS KP' => [
                ['NOREG', 'NAMA', 'KP_ID'],
                ['99000031', 'SHARED STRING TEST', 'TPS KP 3'],
            ],
        ], sharedStrings: true);

        $sheets = (new XlsxReader(preferNative: false))->sheets($file->getRealPath());

        $this->assertSame('99000031', $sheets['TPS KP'][1][0]);
        $this->assertSame('SHARED STRING TEST', $sheets['TPS KP'][1][1]);
        $this->assertSame('TPS KP 3', $sheets['TPS KP'][1][2]);
    }

    /**
     * Build a real .xlsx file so the reader can parse it.
     *
     * By default cells use inline strings; pass $sharedStrings to emit a
     * proper shared-string table (as real Excel exports do).
     *
     * @param  array<string, array<int, array<int, string>>>  $sheets
     */
    private function uploadedWorkbook(array $sheets, bool $sharedStrings = false): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::OVERWRITE);

        $sheetNames = array_keys($sheets);
        $sharedTable = [];

        $contentTypes = ['<?xml version="1.0" encoding="UTF-8" standalone="yes"?>',
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">',
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>',
            '<Default Extension="xml" ContentType="application/xml"/>',
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'];

        $workbookSheets = [];
        $workbookRels = ['<?xml version="1.0" encoding="UTF-8" standalone="yes"?>',
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'];

        foreach ($sheetNames as $index => $name) {
            $n = $index + 1;
            $contentTypes[] = '<Override PartName="/xl/worksheets/sheet' . $n . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
            $workbookSheets[] = '<sheet name="' . $this->xml($name) . '" sheetId="' . $n . '" r:id="rId' . $n . '"/>';
            $workbookRels[] = '<Relationship Id="rId' . $n . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $n . '.xml"/>';

            $zip->addFromString(
                'xl/worksheets/sheet' . $n . '.xml',
                $sharedStrings
                    ? $this->sheetXmlShared($sheets[$name], $sharedTable)
                    : $this->sheetXml($sheets[$name])
            );
        }

        if ($sharedStrings) {
            $contentTypes[] = '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>';
            $zip->addFromString('xl/sharedStrings.xml', $this->sharedStringsXml($sharedTable));
        }

        $contentTypes[] = '</Types>';
        $workbookRels[] = '</Relationships>';

        $zip->addFromString('[Content_Types].xml', implode('', $contentTypes));
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>'
            . implode('', $workbookSheets) . '</sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', implode('', $workbookRels));

        $zip->close();

        return new UploadedFile($path, 'import.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    /**
     * @param  array<int, array<int, string>>  $rows
     */
    private function sheetXml(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $xml .= '<row r="' . ($rowIndex + 1) . '">';

            foreach ($row as $columnIndex => $value) {
                $ref = $this->columnLetter($columnIndex) . ($rowIndex + 1);
                $xml .= '<c r="' . $ref . '" t="inlineStr"><is><t>' . $this->xml($value) . '</t></is></c>';
            }

            $xml .= '</row>';
        }

        return $xml . '</sheetData></worksheet>';
    }

    /**
     * @param  array<int, array<int, string>>  $rows
     * @param  array<string, int>  $sharedTable
     */
    private function sheetXmlShared(array $rows, array &$sharedTable): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $xml .= '<row r="' . ($rowIndex + 1) . '">';

            foreach ($row as $columnIndex => $value) {
                $ref = $this->columnLetter($columnIndex) . ($rowIndex + 1);

                if ($value === '') {
                    continue;
                }

                if (! isset($sharedTable[$value])) {
                    $sharedTable[$value] = count($sharedTable);
                }

                $xml .= '<c r="' . $ref . '" t="s"><v>' . $sharedTable[$value] . '</v></c>';
            }

            $xml .= '</row>';
        }

        return $xml . '</sheetData></worksheet>';
    }

    /**
     * @param  array<string, int>  $sharedTable
     */
    private function sharedStringsXml(array $sharedTable): string
    {
        $items = '';

        foreach (array_keys($sharedTable) as $value) {
            $items .= '<si><t>' . $this->xml($value) . '</t></si>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . count($sharedTable) . '" uniqueCount="' . count($sharedTable) . '">'
            . $items . '</sst>';
    }

    private function columnLetter(int $index): string
    {
        $letters = '';
        $index++;

        while ($index > 0) {
            $remainder = ($index - 1) % 26;
            $letters = chr(65 + $remainder) . $letters;
            $index = intdiv($index - 1, 26);
        }

        return $letters;
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
