<?php

namespace InvoiceService\Contracts;

interface CacheServiceInterface
{
    public function get($key): mixed;
    public function set($key, $value, $expiration): void;
    public function delete($key): void;
}