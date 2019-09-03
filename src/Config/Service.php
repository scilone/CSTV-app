<?php

namespace App\Config;

Class Service
{
    const CONTROLLER_TEST     = 'App\Controller\TestController';
    const CONTROLLER_HOME     = 'App\Controller\HomeController';
    const CONTROLLER_CATEGORY = 'App\Controller\CategoryController';
    const CONTROLLER_STREAM   = 'App\Controller\StreamsController';
    const CONTROLLER_EXTRA    = 'App\Controller\ExtraController';

    const APPLICATION_TWIG       = 'App\Application\Twig';
    const APPLICATION_IPTV       = 'App\Application\Iptv';

    const DOMAIN_IPTV_XCODE_API = 'App\Domain\Iptv\XcodeApi';

    const INFRASTRUCTURE_SUPERGLOBALES = 'App\Infrastructure\SuperglobalesOO';
    const INFRASTRUCTURE_CURL          = 'App\Infrastructure\CurlOO';
    const INFRASTRUCTURE_CACHE_RAW     = 'App\Infrastructure\CacheRaw';
    const INFRASTRUCTURE_CACHE_ITEM    = 'App\Infrastructure\CacheItem';
}
