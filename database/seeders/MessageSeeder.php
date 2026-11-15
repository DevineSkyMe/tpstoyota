<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('messages')->truncate();

        DB::table('messages')->insert([
                        [
                'id' => 1,
                'sender_id' => 1,
                'receiver_id' => 5,
                'related_type' => 'workshop_score_report',
                'related_id' => 1,
                'message' => 'Yth. Direktur TPS, berikut kami sampaikan Laporan Nilai Evaluasi Peserta Workshop TPS-G Batch 1 2026 untuk menjadi periksa.',
                'attachment_path' => 'reports/score_reports/tpsg_batch_1_2026_results.pdf',
                'is_read' => 1,
                'read_at' => '2026-07-28 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 2,
                'sender_id' => 1,
                'receiver_id' => 3,
                'related_type' => 'workshop_score_report',
                'related_id' => 1,
                'message' => 'Laporan Hasil Workshop TPS-G Batch 1 telah selesai. Mohon dapat diproses untuk penerbitan SK kelulusan level KP3 & KP4.',
                'attachment_path' => 'reports/score_reports/tpsg_batch_1_2026_results.pdf',
                'is_read' => 1,
                'read_at' => '2026-07-27 09:34:18',
                'created_at' => '2026-09-20 09:34:18',
                'updated_at' => '2026-09-20 09:34:18',
            ],
                        [
                'id' => 9,
                'sender_id' => 1,
                'receiver_id' => 17,
                'related_type' => 'manual_message',
                'related_id' => null,
                'message' => 'asd ashdajsdpas',
                'attachment_path' => null,
                'is_read' => 0,
                'read_at' => null,
                'created_at' => '2026-11-14 02:18:06',
                'updated_at' => '2026-11-14 02:18:06',
            ],
                        [
                'id' => 30,
                'sender_id' => 1,
                'receiver_id' => 2,
                'related_type' => 'workshop_score_report',
                'related_id' => 2,
                'message' => 'Laporan hasil Workshop TPS-G 2026 Batch 2 telah diterbitkan. Silakan buka detail laporan untuk melihat nilai dan keputusan kelulusan peserta.',
                'attachment_path' => 'http://127.0.0.1:8000/reports/workshop/2',
                'is_read' => 1,
                'read_at' => '2026-11-14 12:21:10',
                'created_at' => '2026-11-14 12:15:42',
                'updated_at' => '2026-11-14 12:21:10',
            ],
                        [
                'id' => 31,
                'sender_id' => 1,
                'receiver_id' => 3,
                'related_type' => 'workshop_score_report',
                'related_id' => 2,
                'message' => 'Laporan hasil Workshop TPS-G 2026 Batch 2 telah diterbitkan. Silakan buka detail laporan untuk melihat nilai dan keputusan kelulusan peserta.',
                'attachment_path' => 'http://127.0.0.1:8000/reports/workshop/2',
                'is_read' => 0,
                'read_at' => null,
                'created_at' => '2026-11-14 12:15:42',
                'updated_at' => '2026-11-14 12:15:42',
            ],
                        [
                'id' => 32,
                'sender_id' => 1,
                'receiver_id' => 5,
                'related_type' => 'workshop_score_report',
                'related_id' => 2,
                'message' => 'Laporan hasil Workshop TPS-G 2026 Batch 2 telah diterbitkan. Silakan buka detail laporan untuk melihat nilai dan keputusan kelulusan peserta.',
                'attachment_path' => 'http://127.0.0.1:8000/reports/workshop/2',
                'is_read' => 0,
                'read_at' => null,
                'created_at' => '2026-11-14 12:15:42',
                'updated_at' => '2026-11-14 12:15:42',
            ],
                        [
                'id' => 57,
                'sender_id' => 1,
                'receiver_id' => 2,
                'related_type' => 'workshop_score_report',
                'related_id' => 1,
                'message' => 'Laporan hasil Workshop TPS-G 2026 Batch 1 telah diterbitkan. Silakan buka detail laporan untuk melihat nilai dan keputusan kelulusan peserta.',
                'attachment_path' => 'reports/score_reports/tpsg_batch_1_2026_results.pdf',
                'is_read' => 0,
                'read_at' => null,
                'created_at' => '2026-11-15 02:16:30',
                'updated_at' => '2026-11-15 02:16:30',
            ],
        ]);

    }
}