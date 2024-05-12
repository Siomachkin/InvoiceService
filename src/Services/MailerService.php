<?php

namespace InvoiceService\Services;

use InvoiceService\Contracts\MailerInterface;

class MailerService implements MailerInterface
{
    public function send($to, $subject, $body, $attachment = null): bool
    {
        return true;
    }
}
