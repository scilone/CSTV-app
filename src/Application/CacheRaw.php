<?php

namespace App\Application;

use App\Infrastructure\SuperglobalesOO;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class CacheRaw extends \App\Infrastructure\CacheRaw
{
    private const PREFIX = './cache/';

    public function __construct(SuperglobalesOO $superglobales)
    {
        parent::__construct($superglobales);
    }

    public function setCache(string $cacheName, string $value): void
    {
        parent::setCache(self::PREFIX . $cacheName, $value);
    }

    public function getCache(string $cacheName, string $expire = null): ?string
    {
        return parent::getCache(self::PREFIX . $cacheName, $expire);
    }
}
