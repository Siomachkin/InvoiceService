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
        $logFile = getenv('LOG_FILE');
        $channel = 'app';
        $logFilePath = __DIR__ . '/../logs/'. $logFile;
        $this->logger = new Logger($channel);
        $this->logger->pushHandler(new StreamHandler($logFilePath, Logger::DEBUG));
    }

    public function info($message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
}