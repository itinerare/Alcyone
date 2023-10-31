<?php

namespace App\Mail;

use App\Models\Report\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportAccepted extends Mailable {
    use Queueable, SerializesModels;

    /**
     * The report instance.
     *
     * @var \App\Models\Report\Report
     */
    public $report;

    /**
     * Create a new message instance.
     */
    public function __construct(Report $report) {
        $this->afterCommit();
        $this->report = $report->withoutRelations();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope {
        return new Envelope(
            subject: 'Content Report Accepted',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content() {
        return new Content(
            markdown: 'mail.report_accepted',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array {
        return [];
    }
}
