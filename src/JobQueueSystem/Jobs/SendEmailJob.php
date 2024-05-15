<?php
namespace InvoiceService\JobQueueSystem\Jobs;

use InvoiceService\Contracts\InvoiceRepositoryInterface;
use InvoiceService\Contracts\MailerInterface;
use InvoiceService\EventsSystem\Contracts\SubjectInterface;
use InvoiceService\EventsSystem\SubjectTrait;
use InvoiceService\JobQueueSystem\Contracts\JobInterface;

class SendEmailJob implements JobInterface, SubjectInterface
{
    use SubjectTrait;

    private array $data;
    private bool $isSended;

    private MailerInterface $mailer;
    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(array $data, MailerInterface $mailer, InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->data = $data;
        $this->mailer = $mailer;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function handle(): void
    {
        if($this->invoiceRepository->isInvoiceUnsent($this->getInvoiceNumber())){
            $this->isSended = $this->mailer->send($this->data['email'], $this->data['subject'], $this->data['body'], $this->data['attachment']);
            $this->notifyObservers();
        }       
    }

    public function isSended(): bool{
        return $this->isSended;
    }

    public function getInvoiceNumber(): string{
        return $this->data['invoice_number'];
    }
}