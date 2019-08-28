<?php

namespace App\Application;

use App\Domain\Iptv\DTO\Category;
use App\Domain\Iptv\DTO\Live;
use App\Domain\Iptv\DTO\Movie;
use App\Domain\Iptv\DTO\Serie;
use App\Infrastructure\CurlOO;
use App\Infrastructure\SuperglobalesOO;
use DateTimeImmutable;

class Iptv
{
    const PREFIX = 'IPTV_';
    const PLAYER_DEEPLINK = 'vlc://';

    /**
     * @var CurlOO
     */
    private $curl;

    /**
     * @var SuperglobalesOO
     */
    private $superglobales;

    /**
     * Iptv constructor.
     *
     * @param CurlOO          $curl
     * @param SuperglobalesOO $superglobales
     */
    public function __construct(CurlOO $curl, SuperglobalesOO $superglobales)
    {
        $this->curl          = $curl;
        $this->superglobales = $superglobales;
    }

    private function getPostData(string $action = ''): array
    {
        return [
            'username' => $this->superglobales->getSession()->get(self::PREFIX . 'username'),
            'password' => $this->superglobales->getSession()->get(self::PREFIX . 'password'),
            'action'   => $action
        ];
    }

    private function getDataWithAction(string $action)
    {
        $this->curl
            ->init($this->superglobales->getSession()->get(self::PREFIX . 'host') . '/player_api.php')
            ->setOption(CURLOPT_RETURNTRANSFER, true)
            ->setOption(CURLOPT_POST, true)
            ->setOption(CURLOPT_POSTFIELDS, http_build_query($this->getPostData($action)));

        return $this->curl->execute();
    }

    private function getCategories($action)
    {
        $list = json_decode($this->getDataWithAction($action));

        foreach ($list as $data) {
            yield new Category(
                $data->category_id,
                $data->category_name,
                $data->parent_id
            );
        }
    }

    public function getLiveCategories()
    {
        return $this->getCategories('get_live_categories');
    }

    public function getMovieCategories()
    {
        return $this->getCategories('get_vod_categories');
    }

    public function getSerieCategories()
    {
        return $this->getCategories('get_series_categories');
    }

    public function getLiveStreams($category)
    {
        $list = json_decode($this->getDataWithAction('get_live_streams'));

        foreach ($list as $data) {
            if (is_numeric($category) && $data->category_id != $category) {
                continue;
            }

            $streamLink = self::PLAYER_DEEPLINK .
                          $this->superglobales->getSession()->get(self::PREFIX . 'host') .
                          '/' . $this->superglobales->getSession()->get(self::PREFIX . 'username') .
                          '/' . $this->superglobales->getSession()->get(self::PREFIX . 'password') .
                          '/' . $data->stream_id;

            $img = '/asset/img/' . base64_encode($data->stream_icon ?? '');

            $name = $data->name ?? '';
            if (is_numeric($category)) {
                $name = trim(preg_replace('#\|\w+\|#', '', $name));
            }

            yield new Live(
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
    }

    public function getMovieStreams($category): array
    {
        $list = json_decode($this->getDataWithAction('get_vod_streams'));

        if ($category === 'recently') {
            $listTmp = [];

            foreach ($list as $data) {
                $listTmp[$data->added] = $data;
            }

            krsort($listTmp);
            $list = $listTmp;
        }

        $i = 0;
        $return = [];
        foreach ($list as $data) {
            if (is_numeric($category) && $data->category_id != $category) {
                continue;
            }

            $i++;

            if ($category === 'recently' && $i > 100) {
                return $return;
            }

            $streamLink = self::PLAYER_DEEPLINK .
                          $this->superglobales->getSession()->get(self::PREFIX . 'host') .
                          '/movie' .
                          '/' . $this->superglobales->getSession()->get(self::PREFIX . 'username') .
                          '/' . $this->superglobales->getSession()->get(self::PREFIX . 'password') .
                          '/' . $data->stream_id . '.' . $data->container_extension;

            $img = '/asset/img/' . base64_encode($data->stream_icon ?? '');

            $name = $data->name ?? '';
            if (is_numeric($category)) {
                $name = trim(preg_replace('#\|\w+\|#', '', $name));
            }

            $return[] = new Movie(
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

        return $return;
    }

    public function getSerieStreams($category)
    {
        $list = json_decode($this->getDataWithAction('get_series'));

        foreach ($list as $data) {
            if (is_numeric($category) && $data->category_id != $category) {
                continue;
            }

            $img = '/asset/img/' . base64_encode($data->cover ?? '');

            $backdrop = [];
            foreach ($data->backdrop_path as $value) {
                $backdrop[] = 'asset/img/' . base64_encode($value);
            }

            yield new Serie(
                (int) $data->num ?? 0,
                (string) $data->name ?? '',
                (int) $data->series_id ?? 0,
                $img,
                (string) $data->plot ?? '',
                (string) $data->cast ?? '',
                (string) $data->director ?? '',
                (string) $data->genre ?? '',
                new DateTimeImmutable($data->releaseDate ?? 0),
                DateTimeImmutable::createFromFormat('U', $data->last_modified ?? 0),
                (float) $data->rating ?? 0,
                (float) $data->rating_5based ?? 0,
                $backdrop,
                (string) $data->youtube_trailer ?? '',
                (int) $data->episode_run_time ?? 0,
                (int) $data->category_id ?? 0
            );
        }
    }
}
