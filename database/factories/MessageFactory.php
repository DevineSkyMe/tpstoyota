<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        $messages = [
            'Berikut terlampir laporan nilai hasil evaluasi peserta Workshop TPS-G Batch ini untuk ditinjau.',
            'Hasil penilaian OMDD telah selesai direkap dan siap didistribusikan ke jajaran Manajemen.',
            'Mohon arahan dan persetujuan untuk penerbitan sertifikat kelulusan level KP3 & KP4.',
            'Laporan evaluasi batch telah diterima, terima kasih atas laporannya.',
        ];

        $isRead = fake()->boolean(70);

        return [
            'sender_id' => UserAccount::factory()->admin(),
            'receiver_id' => UserAccount::factory()->director(),
            'related_type' => 'workshop_score_report',
            'related_id' => null,
            'message' => fake()->randomElement($messages),
            'attachment_path' => 'reports/score_reports/ws_report_' . fake()->uuid() . '.pdf',
            'is_read' => $isRead,
            'read_at' => $isRead ? now() : null,
        ];
    }
}

