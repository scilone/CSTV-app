<?php

namespace App\Application;

use App\Domain\Iptv\DTO\Category;
use App\Domain\Iptv\DTO\Live;
use App\Domain\Iptv\DTO\Movie;
use App\Domain\Iptv\DTO\MovieInfo;
use App\Domain\Iptv\DTO\Serie;
use App\Domain\Iptv\DTO\SerieEpisode;
use App\Domain\Iptv\DTO\SerieInfo;
use App\Domain\Iptv\DTO\SerieSeason;
use App\Domain\Iptv\DTO\Video;
use App\Domain\Iptv\XcodeApi;
use App\Infrastructure\CacheItem;
use App\Infrastructure\CurlOO;
use App\Infrastructure\SodiumDummies;
use App\Infrastructure\SuperglobalesOO;
use DateTimeImmutable;

class Account
{
    /**
     * @var SodiumDummies
     */
    private $sodium;

    /**
     * @var SuperglobalesOO
     */
    private $superglobales;

    public function create(string $username, string $password)
    {
        if ($username === '') {
            return false;
        }


    }
}
