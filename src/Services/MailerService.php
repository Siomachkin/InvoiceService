<?php

namespace InvoiceService\Services;

use InvoiceService\Contracts\CloudStorageInterface;
use InvoiceService\Contracts\MailerInterface;

class MailerService implements MailerInterface
{

    private $cloudStorage;

    public function __construct(CloudStorageInterface $cloudStorage)
    {
        $this->cloudStorage = $cloudStorage;
    }


    public function send($to, $subject, $body, $attachment = null): bool
    {
        return true;
    }
}
