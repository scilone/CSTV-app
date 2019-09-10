<?php

namespace App\Controller;

use App\Application\Iptv;
use App\Config\Param;
use App\Infrastructure\SuperglobalesOO;

class CacheController
{
    /**
     * @var SuperglobalesOO
     */
    private $superglobales;

    public function __construct(SuperglobalesOO $superglobales)
    {
        $this->superglobales = $superglobales;
    }

    public function clearAll()
    {

        $caches = scandir(Param::PREFIX_CACHE);

        foreach ($caches as $cache) {
            if ($cache !== '.' && $cache !== '..') {
                unlink(Param::PREFIX_CACHE . $cache);
            }
        }

        header('Location: ' . Param::BASE_URL_ABSOLUTE . Param::HOME_URL_RELATIVE);
        exit;
    }

    public function clearMine()
    {
        if ($this->superglobales->getSession()->has(Iptv::PREFIX . 'host')
            && $this->superglobales->getSession()->has(Iptv::PREFIX . 'username')
        ) {
            $baseCacheKey = md5(
                $this->superglobales->getSession()->get(Iptv::PREFIX . 'host') .
                $this->superglobales->getSession()->get(Iptv::PREFIX . 'username')
            );

            $caches = scandir(Param::PREFIX_CACHE);

            foreach ($caches as $cache) {
                if (substr($cache, 0, strlen($baseCacheKey)) === $baseCacheKey) {
                    unlink(Param::PREFIX_CACHE . $cache);
                }
            }
        }

        header('Location: ' . Param::BASE_URL_ABSOLUTE . Param::HOME_URL_RELATIVE);
        exit;
    }
}
