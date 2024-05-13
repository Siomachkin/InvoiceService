<?php

namespace InvoiceService\JobQueueSystem\Contracts;

interface QueueManagerInterface
{
    public function enqueueJob(string $queue, array $jobData): void;
    public function listen(string $queue): void;
    public function stop(): void;
}