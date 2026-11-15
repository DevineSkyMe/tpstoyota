<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->truncate();

        DB::table('departments')->insert([
                        [
                'id' => 1,
                'name' => 'Press Department',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 2,
                'name' => 'Welding Department',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 3,
                'name' => 'Paint Department',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 4,
                'name' => 'Assembly Department',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 5,
                'name' => 'Quality Inspection Department',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 6,
                'name' => 'Kaizen & TPS Promotion Department',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 7,
                'name' => 'HRD & Training Center',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 8,
                'name' => 'OMDD (Operations Management Development Division)',
                'created_at' => '2026-09-20 09:34:13',
                'updated_at' => '2026-09-20 09:34:13',
            ],
                        [
                'id' => 150,
                'name' => 'Body Production & Insp P#1 Dept',
                'created_at' => '2026-11-15 01:27:35',
                'updated_at' => '2026-11-15 01:27:35',
            ],
                        [
                'id' => 151,
                'name' => 'Assy Production #1 & PIO Dept',
                'created_at' => '2026-11-15 01:27:35',
                'updated_at' => '2026-11-15 01:27:35',
            ],
                        [
                'id' => 152,
                'name' => 'Frame Production Dept',
                'created_at' => '2026-11-15 01:27:35',
                'updated_at' => '2026-11-15 01:27:35',
            ],
                        [
                'id' => 153,
                'name' => 'Inhouse Manufacturing TPS Jishuken Dept',
                'created_at' => '2026-11-15 01:27:35',
                'updated_at' => '2026-11-15 01:27:35',
            ],
                        [
                'id' => 154,
                'name' => 'TPS Group, Prod. & Shopfloor Dev. Dept',
                'created_at' => '2026-11-15 01:27:36',
                'updated_at' => '2026-11-15 01:27:36',
            ],
                        [
                'id' => 155,
                'name' => 'Body Production & Insp P#2 Dept',
                'created_at' => '2026-11-15 01:27:36',
                'updated_at' => '2026-11-15 01:27:36',
            ],
                        [
                'id' => 156,
                'name' => 'Painting Production #2 Dept',
                'created_at' => '2026-11-15 01:27:36',
                'updated_at' => '2026-11-15 01:27:36',
            ],
                        [
                'id' => 157,
                'name' => 'Painting Production #1 Dept',
                'created_at' => '2026-11-15 01:27:37',
                'updated_at' => '2026-11-15 01:27:37',
            ],
                        [
                'id' => 158,
                'name' => 'Logistic Operation Vehicle Plant Dept',
                'created_at' => '2026-11-15 01:27:37',
                'updated_at' => '2026-11-15 01:27:37',
            ],
                        [
                'id' => 159,
                'name' => 'Quality Inspection 1 Dept',
                'created_at' => '2026-11-15 01:27:38',
                'updated_at' => '2026-11-15 01:27:38',
            ],
                        [
                'id' => 160,
                'name' => 'Press Production Karawang Dept',
                'created_at' => '2026-11-15 01:27:38',
                'updated_at' => '2026-11-15 01:27:38',
            ],
                        [
                'id' => 161,
                'name' => 'TNGA Production Dept',
                'created_at' => '2026-11-15 01:27:40',
                'updated_at' => '2026-11-15 01:27:40',
            ],
                        [
                'id' => 162,
                'name' => 'Service Parts Procurement Dept',
                'created_at' => '2026-11-15 01:27:40',
                'updated_at' => '2026-11-15 01:27:40',
            ],
                        [
                'id' => 163,
                'name' => 'Quality Inspection 2 Dept',
                'created_at' => '2026-11-15 01:27:41',
                'updated_at' => '2026-11-15 01:27:41',
            ],
                        [
                'id' => 164,
                'name' => 'Logistic Operation Unit Plant Dept',
                'created_at' => '2026-11-15 01:27:42',
                'updated_at' => '2026-11-15 01:27:42',
            ],
                        [
                'id' => 165,
                'name' => 'Production Dept',
                'created_at' => '2026-11-15 01:27:42',
                'updated_at' => '2026-11-15 01:27:42',
            ],
                        [
                'id' => 166,
                'name' => 'Casting Engineering Service Dept',
                'created_at' => '2026-11-15 01:27:42',
                'updated_at' => '2026-11-15 01:27:42',
            ],
                        [
                'id' => 167,
                'name' => 'TR Production Dept',
                'created_at' => '2026-11-15 01:27:43',
                'updated_at' => '2026-11-15 01:27:43',
            ],
                        [
                'id' => 168,
                'name' => 'Tier-1 & Tier-2 Supp TPS Jishuken Dept',
                'created_at' => '2026-11-15 01:27:44',
                'updated_at' => '2026-11-15 01:27:44',
            ],
                        [
                'id' => 169,
                'name' => 'Service Parts Warehouse Management Dept',
                'created_at' => '2026-11-15 01:27:44',
                'updated_at' => '2026-11-15 01:27:44',
            ],
                        [
                'id' => 170,
                'name' => 'Unit & Part Supply Management Dept',
                'created_at' => '2026-11-15 01:27:45',
                'updated_at' => '2026-11-15 01:27:45',
            ],
                        [
                'id' => 171,
                'name' => 'Inhouse Logistik TPS Jishuken Dept',
                'created_at' => '2026-11-15 01:27:46',
                'updated_at' => '2026-11-15 01:27:46',
            ],
                        [
                'id' => 172,
                'name' => 'Assy Production #2 Dept',
                'created_at' => '2026-11-15 01:27:46',
                'updated_at' => '2026-11-15 01:27:46',
            ],
                        [
                'id' => 173,
                'name' => 'Logistic Planning Dept',
                'created_at' => '2026-11-15 01:27:48',
                'updated_at' => '2026-11-15 01:27:48',
            ],
                        [
                'id' => 174,
                'name' => 'Press Production Sunter Dept',
                'created_at' => '2026-11-15 01:27:51',
                'updated_at' => '2026-11-15 01:27:51',
            ],
                        [
                'id' => 175,
                'name' => 'CKD Procurement & Log Control Dept',
                'created_at' => '2026-11-15 01:27:52',
                'updated_at' => '2026-11-15 01:27:52',
            ],
                        [
                'id' => 176,
                'name' => 'Casting Production Dept',
                'created_at' => '2026-11-15 01:27:52',
                'updated_at' => '2026-11-15 01:27:52',
            ],
                        [
                'id' => 177,
                'name' => 'Tooling Production Dept',
                'created_at' => '2026-11-15 01:27:54',
                'updated_at' => '2026-11-15 01:27:54',
            ],
                        [
                'id' => 178,
                'name' => 'TPS Office Jishuken and Planning Dept',
                'created_at' => '2026-11-15 01:27:54',
                'updated_at' => '2026-11-15 01:27:54',
            ],
                        [
                'id' => 179,
                'name' => 'Plant Personel, Prod. & Cost Ctrl Dept',
                'created_at' => '2026-11-15 01:27:57',
                'updated_at' => '2026-11-15 01:27:57',
            ],
                        [
                'id' => 180,
                'name' => 'Press Welding Maintenance & Workshop Dept',
                'created_at' => '2026-11-15 01:28:00',
                'updated_at' => '2026-11-15 01:28:00',
            ],
                        [
                'id' => 181,
                'name' => 'Assy Production #2',
                'created_at' => '2026-11-15 01:28:05',
                'updated_at' => '2026-11-15 01:28:05',
            ],
                        [
                'id' => 182,
                'name' => '(C) (M) (K) Quality Dept',
                'created_at' => '2026-11-15 01:28:05',
                'updated_at' => '2026-11-15 01:28:05',
            ],
                        [
                'id' => 183,
                'name' => 'Maintenance Dept',
                'created_at' => '2026-11-15 01:28:06',
                'updated_at' => '2026-11-15 01:28:06',
            ],
                        [
                'id' => 184,
                'name' => 'Tooling Engineering Dept',
                'created_at' => '2026-11-15 01:28:06',
                'updated_at' => '2026-11-15 01:28:06',
            ],
                        [
                'id' => 185,
                'name' => 'Assy & Painting Maintenance Dept',
                'created_at' => '2026-11-15 01:28:07',
                'updated_at' => '2026-11-15 01:28:07',
            ],
                        [
                'id' => 186,
                'name' => 'Plant Utility & Building Maint. Dept',
                'created_at' => '2026-11-15 01:28:07',
                'updated_at' => '2026-11-15 01:28:07',
            ],
                        [
                'id' => 187,
                'name' => 'TR Engineering Service Dept',
                'created_at' => '2026-11-15 01:28:09',
                'updated_at' => '2026-11-15 01:28:09',
            ],
                        [
                'id' => 188,
                'name' => 'Equipment Development Dept',
                'created_at' => '2026-11-15 01:28:11',
                'updated_at' => '2026-11-15 01:28:11',
            ],
                        [
                'id' => 189,
                'name' => 'Engineering Service Dept',
                'created_at' => '2026-11-15 01:28:11',
                'updated_at' => '2026-11-15 01:28:11',
            ],
                        [
                'id' => 190,
                'name' => 'PPIC & Warehouse Dept',
                'created_at' => '2026-11-15 01:28:11',
                'updated_at' => '2026-11-15 01:28:11',
            ],
                        [
                'id' => 191,
                'name' => 'PGA & SHE Dept',
                'created_at' => '2026-11-15 01:28:16',
                'updated_at' => '2026-11-15 01:28:16',
            ],
                        [
                'id' => 192,
                'name' => 'Inhouse Manufacturing TPS Jishuken',
                'created_at' => '2026-11-15 01:28:18',
                'updated_at' => '2026-11-15 01:28:18',
            ],
        ]);

    }
}