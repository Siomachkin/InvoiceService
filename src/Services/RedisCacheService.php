<?php
namespace InvoiceService\Services;

use InvoiceService\Contracts\CacheServiceInterface;
use InvoiceService\Traits\RedisConnectionTrait;

class RedisCacheService implements CacheServiceInterface
{
    use RedisConnectionTrait;

    public function __construct()
    {
        $this->connectToRedis();
    }

    public function get($key) :mixed
    {
        return $this->redis->get($key);
    }

    public function set($key, $value, $expiration) :void
    {
        $this->redis->setex($key, $expiration, $value);
    }

    public function delete($key) :void
    {
        $this->redis->del($key);
    }
}