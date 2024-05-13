<?php
namespace InvoiceService\JobQueueSystem\Jobs;

use InvoiceService\Contracts\MailerInterface;
use InvoiceService\JobQueueSystem\Contracts\JobInterface;

class SendEmailJob implements JobInterface {
    protected array $data;

    private MailerInterface $mailer;

    public function __construct(array $data, MailerInterface $mailer) {
        $this->data = $data;
        $this->mailer = $mailer;
    }

    public function handle(): void {
        
    }
}