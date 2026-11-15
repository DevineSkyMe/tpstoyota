<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ActivityReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $portalUrl;

    public function __construct(
        public Collection $activities,
        public array $summary,
        public array $filters,
        ?string $portalUrl = null
    ) {
        $this->portalUrl = $portalUrl ?: route('reports.activity');
    }

    public function envelope(): Envelope
    {
        $period = ($this->filters['start_date'] ?? '-') . ' s/d ' . ($this->filters['end_date'] ?? '-');

        return new Envelope(
            subject: "Laporan Activity Kaizen TPS-G ({$period})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.activity_report',
        );
    }

    /**
     * Generate PDF attachment from the activity report view.
     */
    public function attachments(): array
    {
        $pdf = Pdf::loadView('reports.activity_pdf', [
            'activities' => $this->activities,
            'summary'    => $this->summary,
            'filters'    => $this->filters,
        ]);

        $pdf->setPaper('A4', 'landscape');

        $start   = str_replace('-', '', $this->filters['start_date'] ?? now()->format('Ymd'));
        $end     = str_replace('-', '', $this->filters['end_date']   ?? now()->format('Ymd'));
        $filename = "Laporan_Activity_Kaizen_{$start}_{$end}.pdf";

        return [
            Attachment::fromData(fn () => $pdf->output(), $filename)
                ->withMime('application/pdf'),
        ];
    }
}
