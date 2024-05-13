<?php

namespace InvoiceService\Controllers;


use InvoiceService\EventsSystem\Contracts\ObserverInterface;
use InvoiceService\EventsSystem\Contracts\SubjectInterface;
use InvoiceService\JobQueueSystem\Contracts\QueueManagerInterface;
use InvoiceService\Contracts\InvoiceRepositoryInterface;
use InvoiceService\JobQueueSystem\Jobs\GeneratePdfJob;

class InvoiceController implements ObserverInterface
{
    private $invoiceRepository;
    private $logger;

    private $queueManager;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository, QueueManagerInterface $queueManager)
    {   
        $this->invoiceRepository = $invoiceRepository;
        $this->queueManager = $queueManager;
    }

    public function createAndSendInvoice(string $email, array $workItems): array
    {

        //$this->logger->info("");

        $clientData = $this->invoiceRepository->findClientByEmail($email);
        $companyData = $this->invoiceRepository->findCompanyByClientEmail($email);
        $invoice = $this->invoiceRepository->createInvoice($email);

        if (!$invoice || !$clientData || !$companyData) {
            return ['status' => 'error', 'message' => 'Failed to create invoice.'];
        }

        $invoiceJobData = [
            'type' => 'generate_pdf',
            'data' => [
                'invoice_number' => $invoice['invoice_number'],
                'invoice_date' => $invoice['created_at'],
                'client' => $clientData,
                'company' => $companyData,
                'items' => $workItems
            ],
        ];

        $this->queueManager->enqueueJob('queue_invoice', $invoiceJobData);

        return ['status' => 'success', 'message' => 'Invoice created and email sent.'];
    }

    public function onEvent(SubjectInterface $subject): void
    {
        if ($subject instanceof GeneratePdfJob) {
            $pdfDocumentPath = $subject->getPdfPath();
            $email = $subject->getEmail();

            $emailJobData = [
                'type' => 'send_email',
                'data' => [
                    'email' => $email,
                    'subject' => 'Your Invoice',
                    'message' => 'Please see the attached invoice.',
                    'attachmentPath' => $pdfDocumentPath,
                ],
            ];
            
            $this->queueManager->enqueueJob('queue_email', $emailJobData);
        }
    }
}