<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public $content;

    /**
     * Create a new message instance.
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->markdown('emails.newsletter')
                    ->subject('Our Latest Newsletter')
                    ->with('content', $this->content);
    }
}
