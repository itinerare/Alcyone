<?php

namespace App\Mail;

use App\Models\Report\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportSubmitted extends Mailable {
    use Queueable, SerializesModels;

    /**
     * The report instance.
     *
     * @var \App\Models\Report\Report
     */
    public $report;

    /**
     * The image upload instance.
     *
     * @var \App\Models\ImageUpload
     */
    public $image;

    /**
     * Create a new message instance.
     */
    public function __construct(Report $report) {
        $this->afterCommit();
        $this->report = $report;
        $this->image = $report->image;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope {
        return new Envelope(
            subject: 'New Content Report Submitted',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content {
        return new Content(
            markdown: 'mail.report-submitted',
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
