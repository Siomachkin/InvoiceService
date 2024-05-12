<?php

namespace InvoiceService\Traits;

use MongoDB\Client;

trait MongoConnectionTrait {
    protected $client;

    public function connectToMongo($databaseName = 'invoiceServiceDatabase') {
        $this->client = new Client("mongodb://mongo:27017");
        $this->databaseName = $databaseName;
    }
}