<?php

namespace InvoiceService\Contracts;

interface InvoiceGeneratorInterface
{
    public function generate(array $invoiceData): string;
}
