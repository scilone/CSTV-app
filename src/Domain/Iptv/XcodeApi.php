<?php

namespace App\Domain\Iptv;

use App\Infrastructure\CacheRaw;
use App\Infrastructure\CurlOO;

class XcodeApi
{
    /**
     * @var CurlOO
     */
    private $curl;

    /**
     * @var CacheRaw
     */
    private $cache;

    public function __construct(CurlOO $curl, CacheRaw $cache)
    {
        $this->curl  = $curl;
        $this->cache = $cache;
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
            '1 day'
        );

        if ($dataCached !== null) {
            $data = $dataCached;
        } else {
            $data = $this->getDataWithAction($host, $username, $password, $action, $extra);
        }

        return (array) json_decode($data);
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
}
