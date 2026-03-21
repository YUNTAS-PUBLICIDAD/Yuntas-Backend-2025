<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PopupTemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $subjectLine;
    public string $bodyHtml;
    public ?string $imageUrl;

    public function __construct(string $subjectLine, string $bodyHtml, ?string $imageUrl = null)
    {
        $this->subjectLine = $subjectLine;
        $this->bodyHtml = $bodyHtml;
        $this->imageUrl = $imageUrl;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.popup-template')
            ->with([
                'subjectLine' => $this->subjectLine,
                'bodyHtml' => $this->bodyHtml,
                'imageUrl' => $this->imageUrl,
            ]);
    }
}
