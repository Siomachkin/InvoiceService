<?php
namespace InvoiceService;

use DI\Container;
use DI\ContainerBuilder;
use InvoiceService\Contracts\CacheServiceInterface;
use InvoiceService\Contracts\CloudStorageInterface;
use InvoiceService\Contracts\InvoiceGeneratorInterface;
use InvoiceService\Contracts\InvoiceRepositoryInterface;
use InvoiceService\Contracts\LoggerInterface;
use InvoiceService\Contracts\MailerInterface;
use InvoiceService\JobQueueSystem\Contracts\QueueManagerInterface;
use InvoiceService\JobQueueSystem\Jobs\SendEmailJob;
use InvoiceService\JobQueueSystem\RedisQueueManager;
use InvoiceService\JobQueueSystem\Jobs\GeneratePdfJob;
use InvoiceService\Repositories\CachedInvoiceRepositoryProxy;
use InvoiceService\Services\GoogleCloudStorageService;
use InvoiceService\Services\MailerService;
use InvoiceService\Services\MonologLoggerService;
use InvoiceService\Services\MpdfInvoiceGenerator;
use InvoiceService\Services\RedisCacheService;
use InvoiceService\Repositories\MongoInvoiceRepository;
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
            MailerInterface::class => autowire(MailerService::class),
            QueueManagerInterface::class => autowire(RedisQueueManager::class),
            CloudStorageInterface::class => autowire(GoogleCloudStorageService::class),
            LoggerInterface::class => autowire(MonologLoggerService::class),

            MongoInvoiceRepository::class => autowire(MongoInvoiceRepository::class),
            InvoiceRepositoryInterface::class => factory(function (Container $container) {
                $mongoInvoiceRepository = $container->get(MongoInvoiceRepository::class);
                $cacheService = $container->get(CacheServiceInterface::class);
                return new CachedInvoiceRepositoryProxy($mongoInvoiceRepository, $cacheService);
            }),

            GeneratePdfJob::class => factory(function (Container $container, $data) {
                $invoiceGenerator = $container->get(MpdfInvoiceGenerator::class);
                return new GeneratePdfJob($data, $invoiceGenerator);
            }),
            
            SendEmailJob::class => factory(function (Container $container, $data) {
                $mailerService = $container->get(MailerService::class);
                return new SendEmailJob($data, $mailerService);
            }),
        ]);

        return $containerBuilder->build();
    }
}