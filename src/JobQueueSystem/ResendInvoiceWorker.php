<?php

namespace InvoiceService\JobQueueSystem;

require __DIR__ . '/../../vendor/autoload.php';

use InvoiceService\Contracts\InvoiceRepositoryInterface;
use InvoiceService\Contracts\LoggerInterface;
use InvoiceService\JobQueueSystem\Contracts\QueueManagerInterface;
use InvoiceService\ServiceContainer;

class ResendInvoiceWorker
{
    private InvoiceRepositoryInterface $invoiceRepository;
    private QueueManagerInterface $queueManager;
    private LoggerInterface $logger;
    private bool $shouldStop = false;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository, QueueManagerInterface $queueManager, LoggerInterface $logger)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->queueManager = $queueManager;
        $this->logger = $logger;
    }

    public function start()
    {
        $this->shouldStop = false;

        while (!$this->shouldStop) {
            $unsentInvoices = $this->invoiceRepository->getUnsentInvoices();
            foreach ($unsentInvoices as $invoice) {

                $this->enqueueEmailJob($invoice);

            }
            sleep(60);
        }
    }

    private function enqueueEmailJob($invoice)
    {
        $invoiceNumber = $invoice['invoice_number'];
        $email = $invoice['client_email'];
        $pdfDocumentPath = $invoice['invoice_path'];

        $emailJobData = [
            'type' => 'send_email',
            'data' => [
                'invoice_number' => $invoiceNumber,
                'email' => $email,
                'subject' => 'Your Invoice',
                'body' => 'Please see the attached invoice.',
                'attachment' => $pdfDocumentPath,
            ],
        ];

        $this->queueManager->enqueueJob('queue_email', $emailJobData);
        $this->logger->info("Enqueued email job for invoice {$invoiceNumber}", $emailJobData);
    }

    public function stop()
    {
        $this->shouldStop = true;
    }
}

$container = ServiceContainer::buildContainer();
$worker = $container->get(ResendInvoiceWorker::class);
$worker->start();