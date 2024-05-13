<?php
namespace InvoiceService\JobQueueSystem\Jobs;

use InvoiceService\JobQueueSystem\Contracts\JobInterface;
use InvoiceService\EventsSystem\Contracts\SubjectInterface;
use InvoiceService\EventsSystem\SubjectTrait;
use InvoiceService\Contracts\InvoiceGeneratorInterface;

class GeneratePdfJob implements JobInterface, SubjectInterface {

    use SubjectTrait;

    private array $data;
    private string $pdfPath = '';
    private InvoiceGeneratorInterface $invoiceGenerator;

    public function __construct(array $data, InvoiceGeneratorInterface $invoiceGenerator) {
        $this->data = $data;
        $this->invoiceGenerator = $invoiceGenerator;
    }

    public function handle(): void {     
        $this->pdfPath = $this->invoiceGenerator->generate($this->data);
        $this->notifyObservers();
    }

    public function getPdfPath(): string {
        return $this->pdfPath;
    }

    public function getEmail(): string {
        return $this->data['client']['email'];
    }

    public function getInvoiceNumber(): int { 
        return intval($this->data['invoice_number']);
    }
}