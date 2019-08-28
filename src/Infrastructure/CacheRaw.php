<?php

namespace App\Infrastructure;

use DateTime;
use DateTimeImmutable;

class CacheRaw
{
    private const ACTIVE = true;

    /**
     * @var SuperglobalesOO
     */
    private $superglobales;

    public function __construct(SuperglobalesOO $superglobales)
    {
        $this->superglobales = $superglobales;
    }

    private function isCacheEnabled(): bool
    {
        return self::ACTIVE && $this->superglobales->getQuery()->has('force') === false;
    }

    public function setCache(string $cacheName, string $value): void
    {
        if ($this->isCacheEnabled() === false) {
            return;
        }

        $cache = fopen(
            $cacheName,
            'w+'
        );

        fputs($cache, $value);
        fclose($cache);
    }

    public function getCache(string $cacheName, string $expire = null): ?string
    {
        if ($this->isCacheEnabled() === false) {
            return null;
        }

        if (file_exists($cacheName) === false) {
            return null;
        }

        if ($expire !== null) {
            $dateExpire = DateTime::createFromFormat('U', filemtime($cacheName));
            $dateExpire->modify("+ $expire");

            if ($dateExpire < new DateTimeImmutable()) {
                $this->deleteCache($cacheName);

                return null;
            }
        }

        return file_get_contents($cacheName);
    }

    public function deleteCache(string $cacheName): void
    {
        if ($this->isCacheEnabled() === false) {
            return;
        }

        unlink($cacheName);
    }
}
