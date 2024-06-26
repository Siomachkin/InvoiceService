<?php

namespace InvoiceService\JobQueueSystem;

use DI\Container;
use InvoiceService\Controllers\InvoiceController;
use InvoiceService\JobQueueSystem\Contracts\JobInterface;
use InvoiceService\JobQueueSystem\Jobs\GeneratePdfJob;
use InvoiceService\JobQueueSystem\Jobs\SendEmailJob;
use InvoiceService\Repositories\MongoInvoiceRepository;

class JobFactory
{

    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function createJob($type, $data): JobInterface
    {
        switch ($type) {
            case 'generate_pdf':
                $pdfJob = $this->container->make(GeneratePdfJob::class, ['data' => $data]);
                $pdfJobObserver = $this->container->get(InvoiceController::class);
                $pdfJob->attach($pdfJobObserver);
                $invoiceRepository = $this->container->get(MongoInvoiceRepository::class);
                $pdfJob->attach($invoiceRepository);
                return $pdfJob;
            case 'send_email':
                $mailerJob = $this->container->make(SendEmailJob::class, ['data' => $data]);
                $invoiceRepository = $this->container->get(MongoInvoiceRepository::class);
                $mailerJob->attach($invoiceRepository);
                return $mailerJob;
            default:
                throw new \InvalidArgumentException("Unknown job type: " . $type);
        }
    }
}