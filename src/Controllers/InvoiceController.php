<?php

namespace InvoiceService\Controllers;

use InvoiceService\Contracts\MailerInterface;
use InvoiceService\Contracts\InvoiceGeneratorInterface;
use InvoiceService\Contracts\InvoiceRepositoryInterface;
use InvoiceService\Contracts\LoggerInterface;

class InvoiceController
{
    private $mailer;
    private $invoiceGenerator;
    private $invoiceRepository;
    private $logger;

    public function __construct(
        MailerInterface $mailer,
        InvoiceGeneratorInterface $invoiceGenerator,
        InvoiceRepositoryInterface $invoiceRepository,
        //LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->invoiceGenerator = $invoiceGenerator;
        $this->invoiceRepository = $invoiceRepository;
        //$this->logger = $logger;
    }

    public function createAndSendInvoice(string $email, array $workItems) : array
    {

        //$this->logger->info("");

        $clientData = $this->invoiceRepository->findClientByEmail($email);
        $companyData = $this->invoiceRepository->findCompanyByClientEmail($email);
        $invoice = $this->invoiceRepository->createInvoice($email);

        if (!$invoice || !$clientData || !$companyData) {
            return ['status' => 'error', 'message' => 'Failed to create invoice.'];
        }

        $invoiceData = [
            'invoice_number' => $invoice['invoice_number'],
            'invoice_date' => $invoice['created_at'],
            'client' => $clientData,
            'company' => $companyData,
            'items' => $workItems
        ];

        $pdfDocumentPath = $this->invoiceGenerator->generate($invoiceData);

        //$this->showPdfInBrowser($pdfDocumentPath);

        $this->invoiceRepository->updateInvoicePath($invoice['invoice_number'], $pdfDocumentPath);

        //$this->mailer->send($email, 'Invoice', 'Please find attached your invoice.', $pdfDocumentPath);

        return ['status' => 'success', 'message' => 'Invoice created and email sent.'];
    }

    public function showPdfInBrowser(string $pdfFilePath): void
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($pdfFilePath) . '"');
        header('Content-Length: ' . filesize($pdfFilePath));
        readfile($pdfFilePath);
    }

}