<?php

namespace InvoiceService\Contracts;

interface LoggerInterface
{
    public function info($message, array $context = []): void;
    public function error($message, array $context = []): void;
}
