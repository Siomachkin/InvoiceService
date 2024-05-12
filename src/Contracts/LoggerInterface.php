<?php
namespace InvoiceService\Contracts;
interface LoggerInterface
{
    public function info($message): void;
    public function error($message): void;
}
