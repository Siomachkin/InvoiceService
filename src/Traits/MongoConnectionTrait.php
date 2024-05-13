<?php

namespace InvoiceService\Traits;

use MongoDB\Client;

trait MongoConnectionTrait
{
    protected Client $client;
    protected string $databaseName;

    public function connectToMongo(): void
    {
        $mongoHost = getenv('MONGO_HOST') ?: 'mongo';
        $mongoPort = getenv('MONGO_PORT') ?: '27017';
        $databaseName = getenv('MONGO_DATABASE') ?: 'invoiceServiceDatabase';

        try {
            $this->client = new Client("mongodb://{$mongoHost}:{$mongoPort}");
            $this->databaseName = $databaseName;
            $this->client->listDatabases();
        } catch (\Exception $e) {
            error_log('MongoDB connection error: ' . $e->getMessage());
            throw $e;
        }
    }
}