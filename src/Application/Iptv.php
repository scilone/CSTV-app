<?php

namespace App\Application;

use App\Domain\Iptv\DTO\Category;
use App\Domain\Iptv\DTO\Live;
use App\Domain\Iptv\DTO\Movie;
use App\Domain\Iptv\DTO\MovieInfo;
use App\Domain\Iptv\DTO\Serie;
use App\Domain\Iptv\DTO\Video;
use App\Domain\Iptv\XcodeApi;
use App\Infrastructure\CacheItem;
use App\Infrastructure\CurlOO;
use App\Infrastructure\SuperglobalesOO;
use DateTimeImmutable;

class Iptv
{
    public const PREFIX = 'IPTV_';
    public const PLAYER_DEEPLINK = 'vlc://';

    private const CACHE_EXPIRE = '1 week';

    /**
     * @var XcodeApi
     */
    private $xcodeApi;

    /**
     * @var SuperglobalesOO
     */
    private $superglobales;

    /**
     * @var CacheItem
     */
    private $cache;

    public function __construct(XcodeApi $xcodeApi, SuperglobalesOO $superglobales, CacheItem $cache)
    {
        $this->xcodeApi      = $xcodeApi;
        $this->superglobales = $superglobales;
        $this->cache         = $cache;
    }

    private function getFormattedCategories(array $list): array
    {
        $categories = [];
        foreach ($list as $data) {
            $categories[$data->category_id] = new Category(
                $data->category_id,
                $data->category_name,
                $data->parent_id
            );
        }

        return $categories;
    }

    private function getCachePrefix(): string
    {
        return md5($this->superglobales->getSession()->get(self::PREFIX . 'host') .
                   $this->superglobales->getSession()->get(self::PREFIX . 'username'));
    }

    public function getLiveCategories(): array
    {
        $cacheKey = $this->getCachePrefix() . '_getLiveCategories';
        $cachedData = $this->cache->get($cacheKey, self::CACHE_EXPIRE);

        if ($cachedData !== null) {
            return $cachedData;
        }

        $data = $this->getFormattedCategories(
            $this->xcodeApi->getLiveCategories(
                $this->superglobales->getSession()->get(self::PREFIX . 'host'),
                $this->superglobales->getSession()->get(self::PREFIX . 'username'),
                $this->superglobales->getSession()->get(self::PREFIX . 'password')
            )
        );

        $this->cache->set($cacheKey, $data);

        return $data;
    }

    public function getMovieCategories()
    {
        $cacheKey = $this->getCachePrefix() . '_getMovieCategories';
        $cachedData = $this->cache->get($cacheKey, self::CACHE_EXPIRE);

        if ($cachedData !== null) {
            return $cachedData;
        }

        $data = $this->getFormattedCategories(
            $this->xcodeApi->getMovieCategories(
                $this->superglobales->getSession()->get(self::PREFIX . 'host'),
                $this->superglobales->getSession()->get(self::PREFIX . 'username'),
                $this->superglobales->getSession()->get(self::PREFIX . 'password')
            )
        );

        $this->cache->set($cacheKey, $data);

        return $data;
    }

    public function getSerieCategories()
    {
        $cacheKey = $this->getCachePrefix() . '_getSerieCategories';
        $cachedData = $this->cache->get($cacheKey, self::CACHE_EXPIRE);

        if ($cachedData !== null) {
            return $cachedData;
        }

        $data = $this->getFormattedCategories(
            $this->xcodeApi->getSerieCategories(
                $this->superglobales->getSession()->get(self::PREFIX . 'host'),
                $this->superglobales->getSession()->get(self::PREFIX . 'username'),
                $this->superglobales->getSession()->get(self::PREFIX . 'password')
            )
        );

        $this->cache->set($cacheKey, $data);

        return $data;
    }

    public function getLiveStreams(array $filter)
    {
        $cacheKey    = $this->getCachePrefix() . '_getMovieStreams_' . http_build_query($filter);
        $cachedData  = $this->cache->get($cacheKey, self::CACHE_EXPIRE);

        if ($cachedData !== null) {
            return $cachedData;
        }

        $list = $this->xcodeApi->getLiveStreams(
            $this->superglobales->getSession()->get(self::PREFIX . 'host'),
            $this->superglobales->getSession()->get(self::PREFIX . 'username'),
            $this->superglobales->getSession()->get(self::PREFIX . 'password')
        );

        $return = [];
        foreach ($list as $data) {
            if (isset($filter['cat']) && $data->category_id != $filter['cat']) {
                continue;
            }

            $streamLink = self::PLAYER_DEEPLINK .
                          $this->superglobales->getSession()->get(self::PREFIX . 'host') .
                          '/' . $this->superglobales->getSession()->get(self::PREFIX . 'username') .
                          '/' . $this->superglobales->getSession()->get(self::PREFIX . 'password') .
                          '/' . $data->stream_id;

            $img = '/asset/img/' . base64_encode($data->stream_icon ?? '');

            $name = $data->name ?? '';
            if (isset($filter['cat'])) {
                $name = trim(preg_replace('#\|\w+\|#', '', $name));
            }

            $return[] = new Live(
                (int) $data->num ?? 0,
                (string) $name,
                (string) $data->stream_type ?? '',
                (int) $data->stream_id ?? 0,
                $img,
                (int) $data->epg_channel_id ?? 0,
                DateTimeImmutable::createFromFormat('U', $data->added ?? 0),
                (int) $data->category_id ?? 0,
                (string) $data->custom_sid ?? '',
                (int) $data->tv_archive ?? 0,
                (string) $data->direct_source ?? '',
                (int) $data->tv_archive_duration ?? 0,
                $streamLink
            );
        }

        $this->cache->set($cacheKey, $return);

        return $return;
    }

    public function getMovieStreams(array $filter, int $sorted = 0): array
    {
        $cacheKey    = $this->getCachePrefix() . '_getMovieStreams_' . $sorted . '_' . http_build_query($filter);
        $cacheExpire = !isset($filter['cat']) && $sorted === 5 ? '1 day' : self::CACHE_EXPIRE;
        $cachedData  = $this->cache->get($cacheKey, $cacheExpire);

        if ($cachedData !== null) {
            return $cachedData;
        }

        $list = $this->xcodeApi->getMovieStreams(
            $this->superglobales->getSession()->get(self::PREFIX . 'host'),
            $this->superglobales->getSession()->get(self::PREFIX . 'username'),
            $this->superglobales->getSession()->get(self::PREFIX . 'password')
        );

        $return = [];
        foreach ($list as $data) {
            if (isset($filter['cat']) && $data->category_id != $filter['cat']) {
                continue;
            }

            $name = $data->name ?? '';
            if (isset($filter['cat'])) {
                $name = trim(preg_replace('#\|\w+\|#', '', $name));
            }

            if (strpos($name, '***') !== false) {
                continue;
            }

            $streamLink = self::PLAYER_DEEPLINK .
                          $this->superglobales->getSession()->get(self::PREFIX . 'host') .
                          '/movie' .
                          '/' . $this->superglobales->getSession()->get(self::PREFIX . 'username') .
                          '/' . $this->superglobales->getSession()->get(self::PREFIX . 'password') .
                          '/' . $data->stream_id . '.' . $data->container_extension;

            $img = '/asset/img/' . base64_encode($data->stream_icon ?? '');

            switch ($sorted) {
                case 1:
                case 2:
                    $key = strtolower($name);
                    break;
                case 3:
                case 4:
                    $key = number_format($data->rating_5based ?? 0, 1);
                    break;
                case 5:
                case 6:
                    $key = $data->added;
                    break;
                default:
                    $key = '';
            }
            $key .= $data->stream_id;

            $return[$key] = new Movie(
                (int) $data->num ?? 0,
                (string) $name,
                (string) $data->stream_type ?? '',
                (int) $data->stream_id ?? 0,
                $img,
                (float) $data->rating ?? 0,
                (float) $data->rating_5based ?? 0,
                DateTimeImmutable::createFromFormat('U', $data->added ?? 0),
                (int) $data->category_id ?? 0,
                (string) $data->container_extension ?? '',
                (string) $data->custom_sid ?? '',
                (string) $data->direct_source ?? '',
                $streamLink
            );
        }

        if ($sorted > 0) {
            if ($sorted % 2 === 0) {
                ksort($return);
            } else {
                krsort($return);
            }
        }

        $return = array_values($return);

        $this->cache->set($cacheKey, $return);

        return $return;
    }

    public function getSerieStreams(array $filter, int $sorted = 0): array
    {
        $cacheKey    = $this->getCachePrefix() . '_getSerieStreams_' . $sorted . '_' . http_build_query($filter);
        $cacheExpire = !isset($filter['cat']) && $sorted === 5 ? '1 day' : self::CACHE_EXPIRE;
        $cachedData  = $this->cache->get($cacheKey, $cacheExpire);

        if ($cachedData !== null) {
            return $cachedData;
        }

        $list = $this->xcodeApi->getSerieStreams(
            $this->superglobales->getSession()->get(self::PREFIX . 'host'),
            $this->superglobales->getSession()->get(self::PREFIX . 'username'),
            $this->superglobales->getSession()->get(self::PREFIX . 'password')
        );

        $return = [];
        foreach ($list as $data) {
            if (isset($filter['cat']) && $data->category_id != $filter['cat']) {
                continue;
            }

            $name = $data->name ?? '';
            if (isset($filter['cat'])) {
                //$name = trim(preg_replace('#\|\w+\|#', '', $name));
            }

            if (strpos($name, '***') !== false) {
                //continue;
            }

            $img = '/asset/img/' . base64_encode($data->cover ?? '');

            switch ($sorted) {
                case 1:
                case 2:
                    $key = strtolower($name);
                    break;
                case 3:
                case 4:
                    $key = number_format($data->rating_5based ?? 0, 1);
                    break;
                case 5:
                case 6:
                    $key = $data->added;
                    break;
                default:
                    $key = '';
            }
            $key .= $data->series_id;

            $backdrop = [];
            if (is_array($data->backdrop_path)) {
                foreach ($data->backdrop_path as $value) {
                    $backdrop[] = '/asset/img/' . base64_encode($value);
                }
            }

            $dateRelease = $data->releaseDate;
            if (strpos($dateRelease, '/') !== false) {
                $tmp         = explode('/', $dateRelease);
                $dateRelease = "$tmp[2]-$tmp[1]-$tmp[0]";
            }

            if (strtotime($dateRelease) === false) {
                $dateRelease = 'now';
            }


            $return[$key] = new Serie(
                (int) $data->num ?? 0,
                (string) $data->name ?? '',
                (int) $data->series_id ?? 0,
                $img,
                (string) $data->plot ?? '',
                (string) $data->cast ?? '',
                (string) $data->director ?? '',
                (string) $data->genre ?? '',
                new DateTimeImmutable($dateRelease),
                DateTimeImmutable::createFromFormat('U', $data->last_modified ?? 0),
                (float) $data->rating ?? 0,
                (float) $data->rating_5based ?? 0,
                $backdrop,
                (string) $data->youtube_trailer ?? '',
                (int) $data->episode_run_time ?? 0,
                (int) $data->category_id ?? 0
            );
        }

        if ($sorted > 0) {
            if ($sorted % 2 === 0) {
                ksort($return);
            } else {
                krsort($return);
            }
        }

        $return = array_values($return);

        $this->cache->set($cacheKey, $return);

        return $return;
    }

    public function getMovieInfo(int $id)
    {
        $cacheKey   = $this->superglobales->getSession()->get(self::PREFIX . 'host') . '_getMovieInfo_' . $id;
        $cachedData = $this->cache->get($cacheKey);

        if ($cachedData !== null) {
            return $cachedData;
        }

        $info = $this->xcodeApi->getMovieInfo(
            $this->superglobales->getSession()->get(self::PREFIX . 'host'),
            $this->superglobales->getSession()->get(self::PREFIX . 'username'),
            $this->superglobales->getSession()->get(self::PREFIX . 'password'),
            $id
        );

        $img = '/asset/img/' . base64_encode($info['info']->movie_image ?? '');

        $backdrop = [];
        foreach ($info['info']->backdrop_path as $value) {
            $backdrop[] = '/asset/img/' . base64_encode($value);
        }

        $streamLink = self::PLAYER_DEEPLINK .
                      $this->superglobales->getSession()->get(self::PREFIX . 'host') .
                      '/movie' .
                      '/' . $this->superglobales->getSession()->get(self::PREFIX . 'username') .
                      '/' . $this->superglobales->getSession()->get(self::PREFIX . 'password') .
                      '/' . $info['movie_data']->stream_id . '.' . $info['movie_data']->container_extension;

        $data = new MovieInfo(
            $img,
            $backdrop,
            (int) $info['info']->duration_secs ?? 0,
            (string) $info['info']->duration ?? '',
            new Video(
                (string) $info['info']->video->codec_name ?? '',
                (int) $info['info']->video->width ?? 0,
                (int) $info['info']->video->height ?? 0
            ),
            (int) $info['info']->bitrate ?? 0,
            (string) $info['info']->youtube_trailer ?? '',
            (string) $info['info']->genre ?? '',
            (string) $info['info']->plot ?? '',
            (string) $info['info']->cast ?? '',
            (float) $info['info']->rating ?? 0,
            (string) $info['info']->director ?? '',
            new DateTimeImmutable($info['info']->releasedate ?? 0),
            (int) $info['movie_data']->stream_id ?? 0,
            (string) $info['movie_data']->name ?? '',
            DateTimeImmutable::createFromFormat('U', $info['movie_data']->added ?? 0),
            (int) $info['movie_data']->category_id ?? 0,
            (string) $info['movie_data']->container_extension ?? '',
            (int) $info['movie_data']->custom_sid ?? 0,
            (string) $info['movie_data']->direct_source ?? '',
            $streamLink
        );

        $this->cache->set($cacheKey, $data);

        return $data;
    }

    public function getSerieInfo(int $id)
    {
        $cacheKey   = $this->superglobales->getSession()->get(self::PREFIX . 'host') . '_getSerieInfo_' . $id;
        $cachedData = $this->cache->get($cacheKey);

        if ($cachedData !== null) {
            return $cachedData;
        }

        $info = $this->xcodeApi->getSerieInfo(
            $this->superglobales->getSession()->get(self::PREFIX . 'host'),
            $this->superglobales->getSession()->get(self::PREFIX . 'username'),
            $this->superglobales->getSession()->get(self::PREFIX . 'password'),
            $id
        );

        var_dump($info);
        exit;

        $img = '/asset/img/' . base64_encode($info['info']->movie_image ?? '');

        $backdrop = [];
        foreach ($info['info']->backdrop_path as $value) {
            $backdrop[] = '/asset/img/' . base64_encode($value);
        }

        $streamLink = self::PLAYER_DEEPLINK .
                      $this->superglobales->getSession()->get(self::PREFIX . 'host') .
                      '/movie' .
                      '/' . $this->superglobales->getSession()->get(self::PREFIX . 'username') .
                      '/' . $this->superglobales->getSession()->get(self::PREFIX . 'password') .
                      '/' . $info['movie_data']->stream_id . '.' . $info['movie_data']->container_extension;

        $data = new MovieInfo(
            $img,
            $backdrop,
            (int) $info['info']->duration_secs ?? 0,
            (string) $info['info']->duration ?? '',
            new Video(
                (string) $info['info']->video->codec_name ?? '',
                (int) $info['info']->video->width ?? 0,
                (int) $info['info']->video->height ?? 0
            ),
            (int) $info['info']->bitrate ?? 0,
            (string) $info['info']->youtube_trailer ?? '',
            (string) $info['info']->genre ?? '',
            (string) $info['info']->plot ?? '',
            (string) $info['info']->cast ?? '',
            (float) $info['info']->rating ?? 0,
            (string) $info['info']->director ?? '',
            new DateTimeImmutable($info['info']->releasedate ?? 0),
            (int) $info['movie_data']->stream_id ?? 0,
            (string) $info['movie_data']->name ?? '',
            DateTimeImmutable::createFromFormat('U', $info['movie_data']->added ?? 0),
            (int) $info['movie_data']->category_id ?? 0,
            (string) $info['movie_data']->container_extension ?? '',
            (int) $info['movie_data']->custom_sid ?? 0,
            (string) $info['movie_data']->direct_source ?? '',
            $streamLink
        );

        $this->cache->set($cacheKey, $data);

        return $data;
    }
}
