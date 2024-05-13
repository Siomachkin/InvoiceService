<?php

namespace InvoiceService\JobQueueSystem\Contracts;

interface JobInterface {
    public function handle(): void;
}