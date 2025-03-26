<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Mail;
use App\Mail\CommonMail;

class MailService
{
    protected $defaultEmail;
    protected $defaultName;

    public function __construct()
    {
        $this->defaultEmail = 'info@reach.boats';
        $this->defaultName = 'Reach Boats';
    }

    public function sendMail($to, $subject, $body, $from = null, $cc = [], $bcc = [], $attachments = [])
    {
        $fromEmail = $from ?: $this->defaultEmail;
        $fromName = $this->defaultName;

        $cc = is_array($cc) ? $cc : [];
        $bcc = is_array($bcc) ? $bcc : [];
        $attachments = is_array($attachments) ? $attachments : [];
        
        $mail = new CommonMail($subject, $body, $fromEmail, $fromName, $cc, $bcc, $attachments);
        Mail::to($to)->send($mail);
    }
}
