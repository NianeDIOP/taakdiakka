<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int,string>  $lines  Paragraphes du corps.
     */
    public function __construct(
        public string $subjectLine,
        public string $heading,
        public array $lines = [],
        public ?string $ctaLabel = null,
        public ?string $ctaUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'heading'  => $this->heading,
                'lines'    => $this->lines,
                'ctaLabel' => $this->ctaLabel,
                'ctaUrl'   => $this->ctaUrl,
                'siteName' => Setting::siteName(),
            ],
        );
    }
}
