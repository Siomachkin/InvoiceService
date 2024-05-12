<?php

namespace InvoiceService\Models;

use MongoDB\Model\BSONDocument;
use MongoDB\BSON\UTCDateTime;

class InvoiceModel extends AbstractModel
{
    public function getNextInvoiceNumber(): int
    {
        $lastInvoice = $this->collection->findOne([], ['sort' => ['invoice_number' => -1]]);

        return $lastInvoice ? $lastInvoice['invoice_number'] + 1 : 1;
    }

    public function createInvoice(string $email): ?BSONDocument
    {
        $invoiceNumber = $this->getNextInvoiceNumber();
        $currentDateTime = new UTCDateTime();

        $invoiceData = [
            'client_email' => $email,
            'invoice_number' => $invoiceNumber,
            'created_at' => $currentDateTime,
        ];

        $this->insertOne($invoiceData);

        return $this->findOne(['invoice_number' => $invoiceNumber]);
    }

    public function updateInvoicePath(int $invoiceNumber, string $invoicePath): void
    {
        $this->updateOne(['invoice_number' => $invoiceNumber], ['invoice_path' => $invoicePath]);
    }
}
