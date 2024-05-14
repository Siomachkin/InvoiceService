<?php

namespace InvoiceService\Models;

use MongoDB\BSON\UTCDateTime;

class LogModel extends AbstractModel
{
    public function __construct($collection)
    {
        parent::__construct($collection);
    }

    public function logInsert(string $email, array $workItems, string $operationType): void
    {
        $currentDateTime = new UTCDateTime();

        $logData = [
            'email' => $email,
            'workItems' => $workItems,
            'operationType' => $operationType,
            'createdAt' => $currentDateTime,
        ];

        $this->insertOne($logData);
    }
}