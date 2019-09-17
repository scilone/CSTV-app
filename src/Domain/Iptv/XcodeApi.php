<?php

namespace App\Domain\Iptv;

use App\Infrastructure\CacheRaw;
use App\Infrastructure\CurlOO;

class XcodeApi
{
    private const CACHE_EXPIRE = '1 day';

    /**
     * @var CurlOO
     */
    private $curl;

    /**
     * @var CacheRaw
     */
    private $cache;

    /**
     * @var string
     */
    private $cacheExpire;

    public function __construct(CurlOO $curl, CacheRaw $cache)
    {
        $this->curl  = $curl;
        $this->cache = $cache;

        $this->setCacheExpire();
    }

    private function getCacheExpire(): string
    {
        return $this->cacheExpire;
    }

    public function setCacheExpire(string $cacheExpire = null): XcodeApi
    {
        if ($cacheExpire === null) {
            $cacheExpire = self::CACHE_EXPIRE;
        }

        $this->cacheExpire = $cacheExpire;

        return $this;
    }

    private function getPostData(string $username, string $password, string $action = ''): array
    {
        return [
            'username' => $username,
            'password' => $password,
            'action'   => $action
        ];
    }

    private function getDataWithAction(
        string $host,
        string $username,
        string $password,
        string $action,
        array $extra = []
    ): string {
        $this->curl
            ->init("$host/player_api.php")
            ->setOption(CURLOPT_RETURNTRANSFER, true)
            ->setOption(CURLOPT_POST, true)
            ->setOption(
                CURLOPT_POSTFIELDS,
                http_build_query($this->getPostData($username, $password, $action) + $extra)
            );

        return $this->curl->execute();
    }

    private function get(string $host, string $username, string $password, string $action, array $extra = []): array
    {
        $dataCached = $this->cache->get(
            md5($host . $username) . '_' . $action . http_build_query($extra),
            $this->getCacheExpire()
        );

        if ($dataCached !== null) {
            $data = $dataCached;

            return (array) json_decode($data);
        }

        $error = false;
        $i = 0;
        do {
            $data        = $this->getDataWithAction($host, $username, $password, $action, $extra);
            $dataDecoded = (array) json_decode($data);
            $error       = isset($dataDecoded['error']);

            $i++;
        } while ($error && $i < 10);

        if ($error === false) {
            $this->cache->set(
                md5($host . $username) . '_' . $action . http_build_query($extra),
                $data
            );
        }

        return $dataDecoded;
    }

    public function getLiveCategories(string $host, string $username, string $password): array
    {
        return $this->get($host, $username, $password, 'get_live_categories');
    }

    public function getMovieCategories(string $host, string $username, string $password): array
    {
        return $this->get($host, $username, $password, 'get_vod_categories');
    }

    public function getSerieCategories(string $host, string $username, string $password): array
    {
        return $this->get($host, $username, $password, 'get_series_categories');
    }

    public function getLiveStreams(string $host, string $username, string $password): array
    {
        return $this->get($host, $username, $password, 'get_live_streams');
    }

    public function getMovieStreams(string $host, string $username, string $password): array
    {
        return $this->get($host, $username, $password, 'get_vod_streams');
    }

    public function getSerieStreams(string $host, string $username, string $password): array
    {
        return $this->get($host, $username, $password, 'get_series');
    }

    public function getMovieInfo(string $host, string $username, string $password, int $id): array
    {
        return $this->get($host, $username, $password, 'get_vod_info', ['vod_id' => $id]);
    }

    public function getSerieInfo(string $host, string $username, string $password, int $id): array
    {
        return $this->get($host, $username, $password, 'get_series_info', ['series_id' => $id]);
    }

    public function getAuthentication(string $host, string $username, string $password): array
    {
        return $this->get($host, $username, $password, '');
    }

    public function getShortEPG(string $host, string $username, string $password, int $streamId): array
    {
        $this->setCacheExpire('1 hour');
        $result = $this->get($host, $username, $password, 'get_short_epg', ['stream_id' => $streamId]);
        $this->setCacheExpire();

        return $result;
    }

    public function getReplay(string $host, string $username, string $password, int $streamId): array
    {
        $this->setCacheExpire('1 hour');
        $result = $this->get($host, $username, $password, 'get_simple_data_table', ['stream_id' => $streamId]);
        $this->setCacheExpire();

        return $result;
    }
}
