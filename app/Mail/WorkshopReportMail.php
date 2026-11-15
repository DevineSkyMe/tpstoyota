<?php

namespace App\Mail;

use App\Models\UserAccount;
use App\Models\Workshop;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkshopReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $portalUrl;

    public function __construct(
        public Workshop $workshop,
        public array $summary,
        public ?UserAccount $recipient = null,
        ?string $portalUrl = null
    ) {
        $this->portalUrl = $portalUrl ?: route('reports.workshop', $workshop);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Laporan Hasil Workshop ' . $this->workshop->title . ' (Batch ' . $this->workshop->batch . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.workshop_report',
        );
    }

    /**
     * Generate PDF attachment from the workshop report view.
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('reports.workshop_pdf', [
            'workshop' => $this->workshop,
            'summary'  => $this->summary,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $filename = 'Laporan_Workshop_' . str_replace(' ', '_', $this->workshop->title)
            . '_Batch' . $this->workshop->batch
            . '_' . $this->workshop->year
            . '.pdf';

        return [
            Attachment::fromData(fn () => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }
}
