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
    }

    public function live(string $category, string $name = ''): void
    {
        $cacheName = md5($this->superglobales->getSession()->get(Iptv::PREFIX . 'host')) . '_stream_live_' . $category;
        $cache = $this->cacheRaw->get($cacheName, '1 day');

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

        //$this->cacheRaw->set($cacheName, $render);

        echo $render;
    }

    public function movie(string $category, int $sort = 0, string $search = ''): void
    {
        $search = urldecode($search);

        $cacheName = md5(session_id()) . '_stream_movie_' . $category . '_' . $sort . md5($search);
        $cache = $this->cacheRaw->get($cacheName, '1 day');

        if ($cache !== null) {
            echo $cache;

            return;
        }

        $filter = [];
        if (is_numeric($category)) {
            $filter['cat'] = $category;
        }

        if ($search !== '') {
            $filter['search'] = $search;
        }

        $streams = $this->iptv->getMovieStreams($filter, $sort);
        $categories = $this->iptv->getMovieCategories();

        if ($search !== '') {
            $streams = array_filter($streams, function ($var) use ($search) {
                return stripos($var->getName(), $search) !== false;
            });
        }

        $render = $this->twig->render(
            'streamsMovie.html.twig',
            [
                'streams'    => $streams,
                'type'       => 'movie',
                'sort'       => $sort,
                'search'     => $search,
                'currentCat' => $category,
                'categories' => $categories,
                'catName'    => isset($categories[$category]) ? $categories[$category]->getName() : 'All',
            ]
        );

        //$this->cacheRaw->set($cacheName, $render);

        echo $render;
    }

    public function movieInfo(string $id): void
    {
        $cacheName = md5(session_id()) . '_stream_movie_info_' . $id;
        $cache = $this->cacheRaw->get($cacheName, '1 day');

        if ($cache !== null) {
            echo $cache;

            return;
        }

        $movie = $this->iptv->getMovieInfo($id);

        $render = $this->twig->render(
            'streamsMovieInfo.html.twig',
            [
                'movie' => $movie
            ]
        );

        //$this->cacheRaw->set($cacheName, $render);

        echo $render;
    }

    public function serie(string $category, string $name = ''): void
    {
        echo $this->twig->render('streamsSerie.html.twig');
        return;
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
