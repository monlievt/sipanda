<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DatabaseBackupMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $filePath,
        public string $fileName,
        public string $fileSize,
        public string $backupDate
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[SIPANDA Trenggalek] Salinan Cadangan Database Otomatis - {$this->backupDate}"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.database_backup',
            with: [
                'fileName'   => $this->fileName,
                'fileSize'   => $this->fileSize,
                'backupDate' => $this->backupDate,
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->filePath)
                ->as($this->fileName)
                ->withMime('application/sql'),
        ];
    }
}
