<?php

namespace InvoiceService\Traits;

use Predis\Client as PredisClient;

trait RedisConnectionTrait
{
    protected PredisClient $redis;

    public function connectToRedis(): void
    {
        $scheme = getenv('REDIS_SCHEME') ?: 'tcp';
        $host = getenv('REDIS_HOST') ?: 'redis';
        $port = getenv('REDIS_PORT') ?: 6379;

        try {
            $this->redis = new PredisClient([
                'scheme' => $scheme,
                'host' => $host,
                'port' => $port,
            ]);

            $this->redis->connect();
        } catch (\Exception $e) {
            error_log('Redis connection error: ' . $e->getMessage());
            throw $e;
        }
    }
}