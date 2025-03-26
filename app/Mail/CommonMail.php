<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CommonMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fromAddress;
    public $fromName;
    public $subjectText;
    public $body;
    public $bccAddresses;
    public $ccAddresses;
    public $attachments;

    /**
     * Create a new message instance.
     *
     * @param string $subjectText
     * @param string $body
     * @param string|null $fromAddress
     * @param string|null $fromName
     * @param array|null $ccAddresses
     * @param array|null $bccAddresses
     * @param array|null $attachments
     */
    public function __construct($subjectText, $body, $fromAddress, $fromName, $ccAddresses = [], $bccAddresses = [], $attachments = [])
    {
        $this->subjectText = $subjectText;
        $this->body = $body;
        $this->fromAddress = $fromAddress;
        $this->fromName = $fromName;
        $this->ccAddresses = $ccAddresses;
        $this->bccAddresses = $bccAddresses;
        $this->attachments = $attachments;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return (new Content)
            ->view('emails.email_body')
            ->with(['body' => $this->body]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->from($this->fromAddress, $this->fromName);

        if (!empty($this->ccAddresses)) {
            $email->cc($this->ccAddresses);
        }

        if (!empty($this->bccAddresses)) {
            $email->bcc($this->bccAddresses);
        }

        if (!empty($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                $email->attach($attachment);
            }
        }

        return $email;
    }
}
