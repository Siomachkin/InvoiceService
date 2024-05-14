<?php

namespace InvoiceService\Contracts;

use MongoDB\Model\BSONDocument;

interface InvoiceRepositoryInterface
{
    public function findClientByEmail($email): ?BSONDocument;
    public function findCompanyByClientEmail($email): ?BSONDocument;
    public function createInvoice(string $email): BSONDocument;
    public function updateInvoicePath(int $invoiceNumber, string $invoicePath): void;
    public function logInsert(string $email, array $workItems, string $operationType): void;
}
