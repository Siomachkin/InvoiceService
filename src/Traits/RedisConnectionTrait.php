<?php

namespace InvoiceService\Traits;

use Predis\Client as PredisClient;

trait RedisConnectionTrait {
    protected $redis;

    public function connectToRedis($scheme = 'tcp', $host = 'redis', $port = 6379) {
        $this->redis = new PredisClient([
            'scheme' => $scheme,
            'host' => $host,
            'port' => $port,
        ]);
    }
}