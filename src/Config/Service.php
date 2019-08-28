<?php

namespace App\Config;

Class Service
{
    const CONTROLLER_TEST     = 'App\Controller\TestController';
    const CONTROLLER_HOME     = 'App\Controller\HomeController';
    const CONTROLLER_CATEGORY = 'App\Controller\CategoryController';
    const CONTROLLER_STREAM   = 'App\Controller\StreamsController';

    const APPLICATION_TWIG      = 'App\Application\Twig';
    const APPLICATION_IPTV      = 'App\Application\Iptv';
    const APPLICATION_CACHE_RAW = 'App\Application\CacheRaw';

    const INFRASTRUCTURE_SUPERGLOBALES = 'App\Infrastructure\SuperglobalesOO';
    const INFRASTRUCTURE_CURL = 'App\Infrastructure\CurlOO';
    const INFRASTRUCTURE_CACHE_RAW = 'App\Infrastructure\CacheRaw';
}
