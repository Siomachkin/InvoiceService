<?php

namespace InvoiceService\Repositories;

use InvoiceService\Contracts\InvoiceRepositoryInterface;
use InvoiceService\Contracts\CacheServiceInterface;
use InvoiceService\EventsSystem\Contracts\ObserverInterface;
use InvoiceService\EventsSystem\Contracts\SubjectInterface;
use InvoiceService\JobQueueSystem\Jobs\GeneratePdfJob;
use InvoiceService\Models\LogModel;
use InvoiceService\Traits\MongoConnectionTrait;
use InvoiceService\Models\ClientModel;
use InvoiceService\Models\CompanyModel;
use InvoiceService\Models\InvoiceModel;
use MongoDB\Model\BSONDocument;

class MongoInvoiceRepository implements InvoiceRepositoryInterface, ObserverInterface
{
    use MongoConnectionTrait;
    private CacheServiceInterface $cacheService;
    private ClientModel $clientModel;
    private CompanyModel $companyModel;
    private InvoiceModel $invoiceModel;
    private LogModel $logModel;

    public function __construct(CacheServiceInterface $cacheService)
    {
        $this->connectToMongo();
        $this->cacheService = $cacheService;
        $this->initializeModels();
    }

    private function initializeModels(): void
    {
        $clientsCollection = $this->client->selectCollection($this->databaseName, 'clients');
        $companiesCollection = $this->client->selectCollection($this->databaseName, 'companies');
        $invoiceCollection = $this->client->selectCollection($this->databaseName, 'invoices');
        $logsCollection = $this->client->selectCollection($this->databaseName, 'operation_logs');

        $this->clientModel = new ClientModel($clientsCollection);
        $this->companyModel = new CompanyModel($companiesCollection);
        $this->invoiceModel = new InvoiceModel($invoiceCollection);
        $this->logModel = new LogModel($logsCollection);
    }

    public function findClientByEmail($email): ?BSONDocument
    {
        $cachedClient = $this->cacheService->get('client:' . $email);
        if ($cachedClient) {
            return new BSONDocument(json_decode($cachedClient, true));
        }

        $client = $this->clientModel->findOne(['email' => $email]);
        if ($client) {
            $this->cacheService->set('client:' . $email, json_encode($client->getArrayCopy()), 86400);
        }

        return $client;
    }

    public function findCompanyByClientEmail($email): ?BSONDocument
    {
        return $this->companyModel->findOne(['client_email' => $email]);
    }

    public function createInvoice(string $email): BSONDocument
    {
        return $this->invoiceModel->createInvoice($email);
    }

    public function updateInvoicePath(int $invoiceNumber, string $invoicePath): void
    {
        $this->invoiceModel->updateInvoicePath($invoiceNumber, $invoicePath);
    }

    public function logInsert(string $email, array $workItems, string $operationType): void
    {
        $this->logModel->logInsert($email, $workItems, $operationType);
    }

    public function onEvent(SubjectInterface $subject): void
    {
        if ($subject instanceof GeneratePdfJob) {
            $invoicePath = $subject->getPdfPath();
            $invoiceNumber = $subject->getInvoiceNumber();
            $this->updateInvoicePath($invoiceNumber, $invoicePath);
        }
    }
}
