<?php

namespace InvoiceService\Services;

use InvoiceService\Contracts\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MonologLoggerService implements LoggerInterface
{
    private $logger;

    public function __construct()
    {
        $channel = 'app';
        $logFilePath = __DIR__ . '/../logs/app.log';
        $this->logger = new Logger($channel);
        $this->logger->pushHandler(new StreamHandler($logFilePath, Logger::DEBUG));
    }

    public function info($message): void
    {
        $this->logger->info($message);
    }

    public function error($message): void
    {
        $this->logger->error($message);
    }
}