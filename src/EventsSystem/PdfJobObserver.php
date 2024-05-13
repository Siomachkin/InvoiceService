<?php

namespace InvoiceService\EventsSystem;

use InvoiceService\JobQueueSystem\Contracts\QueueManagerInterface;
use InvoiceService\EventsSystem\Contracts\ObserverInterface;
use InvoiceService\EventsSystem\Contracts\SubjectInterface;
use InvoiceService\JobQueueSystem\Jobs\GeneratePdfJob;

class PdfJobObserver implements ObserverInterface
{
    private $queueManager;

    public function __construct(QueueManagerInterface $queueManager)
    {
        $this->queueManager = $queueManager;
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

            $this->queueManager->enqueueJob('queue', $emailJobData);
        }
    }
}