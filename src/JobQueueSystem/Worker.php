<?php

namespace InvoiceService\JobQueueSystem;

require __DIR__ . '/../../vendor/autoload.php';

use InvoiceService\JobQueueSystem\Contracts\QueueManagerInterface;
use InvoiceService\ServiceContainer;

class Worker
{
    private $queueManager;

    public function __construct(QueueManagerInterface $queueManager)
    {
        $this->queueManager = $queueManager;
    }

    public function start()
    {
        $queueName = getenv('REDIS_QUEUE_NAME') ?: 'queue';
        $this->queueManager->listen($queueName);
    }

    public function stop()
    {
        $this->queueManager->stop();
    }
}

$container = ServiceContainer::buildContainer();
$worker = $container->get(Worker::class);
$worker->start();
