<?php

namespace App\Application;

use App\Domain\Iptv\DTO\Category;
use App\Domain\Iptv\DTO\EpgShort;
use App\Domain\Iptv\DTO\Live;
use App\Domain\Iptv\DTO\Movie;
use App\Domain\Iptv\DTO\MovieInfo;
use App\Domain\Iptv\DTO\Replay;
use App\Domain\Iptv\DTO\Serie;
use App\Domain\Iptv\DTO\SerieEpisode;
use App\Domain\Iptv\DTO\SerieInfo;
use App\Domain\Iptv\DTO\SerieSeason;
use App\Domain\Iptv\DTO\UserInfo;
use App\Domain\Iptv\DTO\Video;
use App\Domain\Iptv\XcodeApi;
use App\Infrastructure\CacheItem;
use App\Infrastructure\SuperglobalesOO;
use DateTimeImmutable;
use DateTimeInterface;

class Iptv
{
    public const PREFIX = 'IPTV_';

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

    public function getReplaysByStreamId(int $streamId, DateTimeInterface $dateStart): array
    {
        $info = $this->xcodeApi->getReplay(
            $this->superglobales->getSession()->get(self::PREFIX . 'host'),
            $this->superglobales->getSession()->get(self::PREFIX . 'username'),
            $this->superglobales->getSession()->get(self::PREFIX . 'password'),
            $streamId
        );

        $replays = [];
        if (isset($info['epg_listings'])) {
            foreach ($info['epg_listings'] as $program) {
                $dateStartReplay = new DateTimeImmutable($program->start ?? null);
                $dateEndReplay   = new DateTimeImmutable($program->end ?? null);

                if ($dateStartReplay < $dateStart || $dateStartReplay->getTimestamp() > time()) {
                    continue;
                }

                $startTs = (int) $program->start_timestamp ?? 0;
                $stopTs  = (int) $program->stop_timestamp ?? 0;

                $streamLink = $this->superglobales->getSession()->get(self::PREFIX . 'host') .
                              '/timeshift' .
                              '/' . $this->superglobales->getSession()->get(self::PREFIX . 'username') .
                              '/' . $this->superglobales->getSession()->get(self::PREFIX . 'password') .
                              '/' . (($stopTs - $startTs) / 60) .
                              '/' . $dateStartReplay->format('Y-m-d:H-i') .
                              '/' . $streamId . '.ts';

                $replays[] = new Replay(
                    (int) $program->id ?? 0,
                    (int) $program->epg_id ?? 0,
                    (string) base64_decode($program->title ?? ''),
                    (string) $program->lang ?? '',
                    $dateStartReplay,
                    $dateEndReplay,
                    (string) base64_decode($program->description ?? ''),
                    (string) $program->channel_id ?? '',
                    $startTs,
                    $stopTs,
                    (int) $program->now_playing ?? 0,
                    (int) $program->has_archive ?? 0,
                    $streamLink
                );
            }
        }

        return $replays;
    }

    public function getLiveStreams(array $filter = [], int $sorted = 0)
    {
        $cacheKey    = $this->getCachePrefix() . '_getLiveStreams_' . $sorted . '_' . http_build_query($filter);
        $cacheExpire = !isset($filter['cat']) && $sorted === 5 ? '1 day' : self::CACHE_EXPIRE;
        $cachedData  = $this->cache->get($cacheKey, $cacheExpire);

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
            if (isset($filter['cat']) && $filter['cat'] !== 'favorites' && $data->category_id != $filter['cat']) {
                continue;
            }

            $streamLink = $this->superglobales->getSession()->get(self::PREFIX . 'host') .
                          '/' . $this->superglobales->getSession()->get(self::PREFIX . 'username') .
                          '/' . $this->superglobales->getSession()->get(self::PREFIX . 'password') .
                          '/' . $data->stream_id;

            $img = '/asset/img/' . base64_encode($data->stream_icon ?? '');

            $name = $data->name ?? '';
            if (isset($filter['cat'])) {
                $name = str_replace(['SD','FHD', 'HD','4K','sd','hd','fhd','4k'], '', $name);
                $name = trim($name);
            }

            $return[$name] = new Live(
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

        if ($sorted > 0) {
            if ($sorted % 2 === 0) {
                ksort($return);
            } else {
                krsort($return);
            }
        }

        return $return;
    }

    private function stripQuality(string $string): string
    {
        return str_replace(['SD','FHD', 'HD','4K','sd','hd','fhd','4k'], '', $string);
    }

    public function getLiveStreamsByName(string $searchedName)
    {
        $searchedName = trim($this->stripQuality($searchedName));

        $cacheKey    = $this->getCachePrefix() . '_getLiveStreamsByName_' . md5($searchedName);
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
            $nameFormatted = $data->name ?? '';
            $nameFormatted = $this->stripQuality($nameFormatted);
            $nameFormatted = trim($nameFormatted);

            if ($nameFormatted !== $searchedName) {
                continue;
            }

            $quality = '';
            if (stripos($data->name, 'SD') !== false) {
                $quality = 'SD';
            } elseif (stripos($data->name, 'FHD') !== false) {
                $quality = 'FHD';
            } elseif (stripos($data->name, '4K') !== false) {
                $quality = '4K';
            } elseif (stripos($data->name, 'HEVC') !== false) {
                $quality = 'HEVC';
            } elseif (stripos($data->name, 'HD') !== false) {
                $quality = 'HD';
            }

            $streamLink = $this->superglobales->getSession()->get(self::PREFIX . 'host') .
                          '/' . $this->superglobales->getSession()->get(self::PREFIX . 'username') .
                          '/' . $this->superglobales->getSession()->get(self::PREFIX . 'password') .
                          '/' . $data->stream_id;

            $img = '/asset/img/' . base64_encode($data->stream_icon ?? '');

            $return[$quality] = new Live(
                (int) $data->num ?? 0,
                (string) $nameFormatted,
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
                $name = trim(preg_replace('#\|[\w\|]+\|#', '', $name));
            }

            if (strpos($name, '***') !== false) {
                continue;
            }

            $streamLink = $this->superglobales->getSession()->get(self::PREFIX . 'host') .
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
                $dateRelease = null;
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

    public function getShortEPG(int $id)
    {
        $info = $this->xcodeApi->getShortEPG(
            $this->superglobales->getSession()->get(self::PREFIX . 'host'),
            $this->superglobales->getSession()->get(self::PREFIX . 'username'),
            $this->superglobales->getSession()->get(self::PREFIX . 'password'),
            $id
        );

        $list = [];
        if (isset($info['epg_listings'])) {
            $info = $info['epg_listings'];

            foreach ($info as $epg) {
                $list[] = new EpgShort(
                    (int) $epg->id ?? 0,
                    (int) $epg->epg_id ?? 0,
                    (string) base64_decode($epg->title ?? ''),
                    (string) $epg->lang ?? '',
                    new DateTimeImmutable($epg->start ?? null),
                    new DateTimeImmutable($epg->end ?? null),
                    (string) base64_decode($epg->description ?? ''),
                    (string) $epg->channel_id ?? '',
                    (int) $epg->start_timestamp ?? 0,
                    (int) $epg->stop_timestamp ?? 0
                );
            }
        }

        return $list;
    }

    public function getMovieInfo(int $id)
    {
        $cacheKey   = $this->getCachePrefix() . '_getMovieInfo_' . $id;
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

        $streamLink = $this->superglobales->getSession()->get(self::PREFIX . 'host') .
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
            new DateTimeImmutable($info['info']->releasedate ?? null),
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
        $cacheKey   = $this->getCachePrefix() . '_getSerieInfo_' . $id;
        $cachedData = $this->cache->get($cacheKey, self::CACHE_EXPIRE);

        if ($cachedData !== null) {
            return $cachedData;
        }

        $data = $this->xcodeApi->getSerieInfo(
            $this->superglobales->getSession()->get(self::PREFIX . 'host'),
            $this->superglobales->getSession()->get(self::PREFIX . 'username'),
            $this->superglobales->getSession()->get(self::PREFIX . 'password'),
            $id
        );

        $dataSeasons  = $data['seasons'];
        $info         = $data['info'];
        $dataEpisodes = $data['episodes'];

        $img = '/asset/img/' . base64_encode($info->cover ?? '');

        $backdrop = [];
        foreach ($info->backdrop_path as $value) {
            $backdrop[] = '/asset/img/' . base64_encode($value);
        }

        $seasons = [];
        foreach ($dataSeasons as $key => $dataSeason) {
            $seasons[$dataSeason->season_number] = new SerieSeason(
                new DateTimeImmutable($dataSeason->air_date ?? null),
                (int) $dataSeason->episode_count ?? 0,
                (int) $dataSeason->id ?? 0,
                (string) $dataSeason->name ?? '',
                (string) $dataSeason->overview ?? '',
                (int) $dataSeason->season_number ?? 0,
                '/asset/img/' . base64_encode($dataSeason->cover ?? ''),
                '/asset/img/' . base64_encode($dataSeason->coverBig ?? '')
            );
        }

        $episodes = [];
        foreach ($dataEpisodes as $seasonNumber => $dataEpisode) {
            foreach ($dataEpisode as $episode) {
                $episodeInfo = $episode->info;

                $streamLink = $this->superglobales->getSession()->get(self::PREFIX . 'host') .
                              '/series' .
                              '/' . $this->superglobales->getSession()->get(self::PREFIX . 'username') .
                              '/' . $this->superglobales->getSession()->get(self::PREFIX . 'password') .
                              '/' . $episode->id . '.' . $episode->container_extension;

                $episodes[$seasonNumber][$episode->episode_num] = new SerieEpisode(
                    (int) $episode->id ?? 0,
                    (int) $episode->episode_num ?? 0,
                    (string) $episode->title ?? '',
                    (string) $episode->container_extension ?? '',
                    (int) $episode->custom_sid ?? 0,
                    DateTimeImmutable::createFromFormat('U', $episode->added ?? 0),
                    (string) $episode->season ?? '',
                    (string) $episode->direct_source ?? '',
                    new Video(
                        $episodeInfo->video->codec_name ?? '',
                        $episodeInfo->video->width ?? 0,
                        $episodeInfo->video->height ?? 0
                    ),
                    '/asset/img/' . base64_encode($episodeInfo->movie_image ?? ''),
                    (string) ($episodeInfo->plot ?? ''),
                    new DateTimeImmutable($episodeInfo->releasedate ?? null),
                    (float) ($episodeInfo->rating ?? 0),
                    $episodeInfo->name ?? '',
                    (int) $episodeInfo->duration_secs ?? 0,
                    (string) $episodeInfo->duration ?? '',
                    (int) $episodeInfo->bitrate ?? 0,
                    $streamLink
                );
            }
        }

        $data = new SerieInfo(
            $id,
            (string) $info->name ?? '',
            $img,
            (string) $info->plot ?? '',
            (string) $info->cast ?? '',
            (string) $info->director ?? '',
            (string) $info->genre ?? '',
            new DateTimeImmutable($info->releaseDate ?? null),
            DateTimeImmutable::createFromFormat('U', $info->added ?? 0),
            (float) $info->rating ?? 0,
            (float) $info->rating_5based ?? 0,
            $backdrop,
            (string) $info->youtube_trailer ?? '',
            (int) $info->episode_run_time ?? 0,
            (int) $info->category_id ?? 0,
            $seasons,
            $episodes
        );

        $this->cache->set($cacheKey, $data);

        return $data;
    }

    public function getUserInfo()
    {
        $data = $this->xcodeApi->getAuthentication(
            $this->superglobales->getSession()->get(self::PREFIX . 'host'),
            $this->superglobales->getSession()->get(self::PREFIX . 'username'),
            $this->superglobales->getSession()->get(self::PREFIX . 'password')
        );

        $userData = $data['user_info'];

        return new UserInfo(
            (string) $userData->username ?? '',
            (string) $userData->password ?? '',
            (string) $userData->message ?? '',
            (int) $userData->auth ?? 0,
            (string) $userData->status ?? '',
            DateTimeImmutable::createFromFormat('U', $userData->exp_date ?? 0),
            (bool) $userData->is_trial ?? true,
            (int) $userData->active_cons ?? 0,
            DateTimeImmutable::createFromFormat('U', $userData->created_at ?? 0),
            (int) $userData->max_connections ?? 0,
            (array) $userData->allowed_output_formats ?? []
        );
    }

    public function getNbStreamByCat(string $type): array
    {
        $cacheKey   = $this->getCachePrefix() . '_getNbStreamByCat_' . $type;
        $cachedData = $this->cache->get($cacheKey, self::CACHE_EXPIRE);

        if ($cachedData !== null) {
            return $cachedData;
        }

        switch ($type) {
            case 'movie':
                $streams    = $this->getMovieStreams([]);
                break;
            case 'live':
                $streams    = $this->getLiveStreams([]);
                break;
            default:
                $streams    = $this->getSerieStreams([]);
        }

        $categoriesNb = [];
        foreach ($streams as $stream) {
            $categoriesNb[$stream->getCategoryId()]++;
        }

        $categoriesNb['all'] = count($streams);

        $this->cache->set($cacheKey, $categoriesNb);

        return $categoriesNb;
    }
}
