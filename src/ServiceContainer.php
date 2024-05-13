<?php
namespace InvoiceService;

use DI\Container;
use DI\ContainerBuilder;
use InvoiceService\Contracts\CacheServiceInterface;
use InvoiceService\Contracts\InvoiceGeneratorInterface;
use InvoiceService\Contracts\InvoiceRepositoryInterface;
use InvoiceService\Contracts\MailerInterface;
use InvoiceService\JobQueueSystem\Contracts\QueueManagerInterface;
use InvoiceService\JobQueueSystem\RedisQueueManager;
use InvoiceService\Services\MailerService;
use InvoiceService\Services\MpdfInvoiceGenerator;
use InvoiceService\Services\RedisCacheService;
use InvoiceService\Repositories\MongoInvoiceRepository;
use InvoiceService\JobQueueSystem\Jobs\GeneratePdfJob;
use function DI\autowire;
use function DI\factory;


class ServiceContainer
{
    public static function buildContainer(): Container
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addDefinitions([
            InvoiceGeneratorInterface::class => autowire(MpdfInvoiceGenerator::class),
            CacheServiceInterface::class => autowire(RedisCacheService::class),
            InvoiceRepositoryInterface::class => autowire(MongoInvoiceRepository::class),
            MailerInterface::class => autowire(MailerService::class),
            QueueManagerInterface::class => autowire(RedisQueueManager::class),

            GeneratePdfJob::class => factory(function (Container $container, $data) {
                $invoiceGenerator = $container->get(MpdfInvoiceGenerator::class);
                return new GeneratePdfJob($data, $invoiceGenerator);
            }),
        ]);

        return $containerBuilder->build();
    }
}