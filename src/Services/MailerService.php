<?php
namespace InvoiceService\Services;

use Mailgun\Mailgun;
use InvoiceService\Contracts\LoggerInterface;
use InvoiceService\Contracts\MailerInterface;

class MailerService implements MailerInterface
{
    private Mailgun $mailgun;
    private string $domain;
    private string $fromEmail;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $apiKey = getenv('MAIL_KEY');
        $this->mailgun = Mailgun::create($apiKey);
        $this->domain = getenv('MAIL_DOMAIN');
        $this->fromEmail = getenv('MAIL_FROM_EMAIL');
        $this->logger = $logger;
    }

    public function send($to, $subject, $body, $attachment): bool
    {
        $message = [
            'from' => $this->fromEmail,
            'to' => $to,
            'subject' => $subject,
            'text' => $body
        ];

        $message['attachment'] = [
            ['filePath' => $attachment, 'filename' => 'Invoice']
        ];
        
        try {
            $this->mailgun->messages()->send($this->domain, $message);
            return true;
        } catch (\Exception $e) {
            $this->logger->error("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
}