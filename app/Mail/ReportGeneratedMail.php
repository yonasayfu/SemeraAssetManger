<?php

namespace App\Mail;

use App\Models\SavedReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportGeneratedMail extends Mailable
{
    use Queueable, SerializesModels;

    public SavedReport $savedReport;
    public $reportResults; // Can be a collection, array, or path to a file

    /**
     * Create a new message instance.
     */
    public function __construct(SavedReport $savedReport, $reportResults)
    {
        $this->savedReport = $savedReport;
        $this->reportResults = $reportResults;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Scheduled Report: ' . $this->savedReport->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reports.generated', // Assuming you'll create this Blade markdown view
            with: [
                'reportName' => $this->savedReport->name,
                'reportResults' => $this->reportResults,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Example of attaching a file if reportResults is a path to a generated file
        // if (is_string($this->reportResults) && file_exists($this->reportResults)) {
        //     return [
        //         Attachment::fromPath($this->reportResults)
        //                   ->as($this->savedReport->name . '.xlsx')
        //                   ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        //     ];
        // }
        return [];
    }
}
