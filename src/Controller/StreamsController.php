<?php

namespace App\Controller;

use App\Application\Iptv;
use App\Application\Twig;
use App\Infrastructure\CacheRaw;
use App\Infrastructure\SuperglobalesOO;

class StreamsController
{
    /**
     * @var Twig
     */
    private $twig;
    /**
     * @var Iptv
     */
    private $iptv;

    /**
     * @var CacheRaw
     */
    private $cacheRaw;

    /**
     * @var SuperglobalesOO
     */
    private $superglobales;

    /**
     * StreamsController constructor.
     *
     * @param Twig            $twig
     * @param Iptv            $iptv
     * @param CacheRaw        $cacheRaw
     * @param SuperglobalesOO $superglobales
     */
    public function __construct(Twig $twig, Iptv $iptv, CacheRaw $cacheRaw, SuperglobalesOO $superglobales)
    {
        $this->twig          = $twig;
        $this->iptv          = $iptv;
        $this->cacheRaw      = $cacheRaw;
        $this->superglobales = $superglobales;

        //Diamond
        $this->superglobales->getSession()
            ->set(Iptv::PREFIX . 'username', '1Vjfv6!P!N')
            ->set(Iptv::PREFIX . 'password', 'yDD38Z5ObO')
            ->set(Iptv::PREFIX . 'host', 'http://netflexx.org:8000');

        //Gold
        /*
        $this->superglobales->getSession()
            ->set(Iptv::PREFIX . 'username', '45165901520581')
            ->set(Iptv::PREFIX . 'password', '45165901520581')
            ->set(Iptv::PREFIX . 'host', 'http://iptv.smartgotv.com:8080');*/
    }

    public function live(string $category, string $name): void
    {
        $cacheName = md5($this->superglobales->getSession()->get(Iptv::PREFIX . 'host')) . '_stream_live_' . $category;
        $cache = $this->cacheRaw->getCache($cacheName, '1 day');

        if ($cache !== null) {
            echo $cache;

            return;
        }

        $streams = $this->iptv->getLiveStreams($category);

        $render = $this->twig->render(
            'streamsLive.html.twig',
            [
                'streams' => $streams,
                'catName' => urldecode($name),
            ]
        );

        $this->cacheRaw->setCache($cacheName, $render);

        echo $render;
    }

    public function movie(string $category, string $name): void
    {
        $cacheName = md5($this->superglobales->getSession()->get(Iptv::PREFIX . 'host')) . '_stream_movie_' . $category;
        $cache = $this->cacheRaw->getCache($cacheName, '1 day');

        if ($cache !== null) {
            echo $cache;

            return;
        }

        $streams = $this->iptv->getMovieStreams($category);

        $render = $this->twig->render(
            'streamsMovie.html.twig',
            [
                'streams' => $streams,
                'catName' => urldecode($name),
            ]
        );

        $this->cacheRaw->setCache($cacheName, $render);

        echo $render;
    }

    public function serie(string $category, string $name): void
    {
        echo '
        <a href="vlc://http://netflexx.org:8000/1Vjfv6!P!N/yDD38Z5ObO/11705">LIVE</a>
        <a href="vlc://http://netflexx.org:8000/movie/1Vjfv6!P!N/yDD38Z5ObO/184204.mkv">MOVIE</a>
        <a href="vlc://http://netflexx.org:8000/series/1Vjfv6!P!N/yDD38Z5ObO/91785.mkv">SERIE</a>
        <a href="vlc://http://netflexx.org:8000/movie/1Vjfv6!P!N/yDD38Z5ObO/184204.mkv" target="_blank">_blank</a>
        <a href="vlc://http://netflexx.org:8000/movie/1Vjfv6!P!N/yDD38Z5ObO/184204.mkv" target="_parent">_parent</a>
        <a href="vlc://http://netflexx.org:8000/movie/1Vjfv6!P!N/yDD38Z5ObO/184204.mkv" target="_self">_self</a>
        <a href="vlc://http://netflexx.org:8000/movie/1Vjfv6!P!N/yDD38Z5ObO/184204.mkv" target="_top">_top</a>
        ';
        /*$streams = $this->iptv->getSerieStreams();

        echo $this->twig->render(
            'category.html.twig',
            [
                'categories' => $categories,
                'type'       => 'movie',
            ]
        );*/
    }
}
