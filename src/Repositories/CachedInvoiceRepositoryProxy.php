<?php

namespace InvoiceService\Repositories;

use InvoiceService\Contracts\InvoiceRepositoryInterface;
use InvoiceService\Contracts\CacheServiceInterface;
use MongoDB\Model\BSONDocument;

class CachedInvoiceRepositoryProxy implements InvoiceRepositoryInterface
{
    private InvoiceRepositoryInterface $invoiceRepository;
    private CacheServiceInterface $cacheService;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository, CacheServiceInterface $cacheService)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->cacheService = $cacheService;
    }

    public function findClientByEmail($email): ?BSONDocument
    {
        $cacheKey = 'client:' . $email;
        $cachedClient = $this->cacheService->get($cacheKey);
        
        if ($cachedClient) {
            return new BSONDocument(json_decode($cachedClient, true));
        }

        $client = $this->invoiceRepository->findClientByEmail($email);
        
        if ($client) {
            $this->cacheService->set($cacheKey, json_encode($client->getArrayCopy()), 86400);
        }

        return $client;
    }


    public function findCompanyByClientEmail($email): ?BSONDocument
{
    $cacheKey = 'company:' . $email;
    $cachedCompany = $this->cacheService->get($cacheKey);
    
    if ($cachedCompany) {
        return new BSONDocument(json_decode($cachedCompany, true));
    }

    $company = $this->invoiceRepository->findCompanyByClientEmail($email);
    
    if ($company) {
        $this->cacheService->set($cacheKey, json_encode($company->getArrayCopy()), 86400);
    }

    return $company;
}

    public function createInvoice(string $email): BSONDocument
    {
        return $this->invoiceRepository->createInvoice($email);
    }

    public function updateInvoicePath(int $invoiceNumber, string $invoicePath): void
    {
        $this->invoiceRepository->updateInvoicePath($invoiceNumber, $invoicePath);
    }

    public function logInsert(string $email, array $workItems, string $operationType): void
    {
        $this->invoiceRepository->logInsert($email, $workItems, $operationType);
    }
}