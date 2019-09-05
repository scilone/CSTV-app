<?php

namespace App\Controller;

use App\Application\Iptv;
use App\Application\Twig;
use App\Config\Param;
use App\Infrastructure\CacheRaw;
use App\Infrastructure\SuperglobalesOO;

class StreamsController extends SecurityController
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

        parent::__construct($superglobales);
    }

    public function play(string $type, string $id, string $season = '', string $episode = '')
    {
        $url = '';
        if ($type === 'movie') {
            $movie = $this->iptv->getMovieInfo($id);
            $url = Param::BASE_URL_ABSOLUTE . '/asset/mkv/' . base64_encode($movie->getStreamLink());
        } elseif ($type === 'serie') {
            $serie = $this->iptv->getSerieInfo($id);

            if (isset($serie->getEpisodes()[$season][$episode])) {
                $url = Param::BASE_URL_ABSOLUTE .
                       '/asset/mkv/' .
                       base64_encode($serie->getEpisodes()[$season][$episode]->getStreamLink());
            }
        }

        if ($url === '') {
            exit;
        }

        echo $this->twig->render(
            'streamsPlay.html.twig',
            [
                'url' => $url,
            ]
        );
    }

    public function live(string $category): void
    {
        $filter = [];
        if (is_numeric($category)) {
            $filter['cat'] = $category;
        }

        $streams    = $this->iptv->getLiveStreams($filter);
        $categories = $this->iptv->getLiveCategories();

        $render = $this->twig->render(
            'streamsLive.html.twig',
            [
                'streams' => $streams,
                'catName'    => isset($categories[$category]) ? $categories[$category]->getName() : '',
            ]
        );

        echo $render;
    }

    public function movie(string $category, int $sort = 0, string $search = ''): void
    {
        $search = urldecode($search);

        $filter = [];
        if (is_numeric($category)) {
            $filter['cat'] = $category;
        }

        if ($search !== '') {
            $filter['search'] = $search;
        }

        $streams = $this->iptv->getMovieStreams($filter, $sort);
        $categories = $this->iptv->getMovieCategories();

        if ($category === 'favorites') {
            $favorites = $this->superglobales->getSession()->get('favorites')['movie'];
            $streams = array_filter($streams, function ($var) use ($favorites) {
                return isset($favorites[$var->getStreamId()]);
            });
        }

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
                'catName'    => isset($categories[$category]) ? $categories[$category]->getName() : '',
            ]
        );

        echo $render;
    }

    public function movieInfo(string $id): void
    {
        $movie = $this->iptv->getMovieInfo($id);

        echo $this->twig->render(
            'streamsMovieInfo.html.twig',
            [
                'movie'      => $movie,
                'isFavorite' => isset($this->superglobales->getSession()->get('favorites')['movie'][$id])
            ]
        );
    }

    public function serieInfo(string $id): void
    {
        $serie = $this->iptv->getSerieInfo($id);

        echo $this->twig->render(
            'streamsSerieInfo.html.twig',
            [
                'serie'      => $serie,
                'isFavorite' => isset($this->superglobales->getSession()->get('favorites')['serie'][$id])
            ]
        );
    }

    public function serie(string $category, int $sort = 0, string $search = ''): void
    {
        $search = urldecode($search);

        $filter = [];
        if (is_numeric($category)) {
            $filter['cat'] = $category;
        }

        if ($search !== '') {
            //$filter['search'] = $search;
        }

        $streams = $this->iptv->getSerieStreams($filter, $sort);
        $categories = $this->iptv->getSerieCategories();

        if ($category === 'favorites') {
            $favorites = $this->superglobales->getSession()->get('favorites')['serie'];
            $streams = array_filter($streams, function ($var) use ($favorites) {
                return isset($favorites[$var->getSerieId()]);
            });
        }

        if ($search !== '') {
            $streams = array_filter($streams, function ($var) use ($search) {
                return stripos($var->getName(), $search) !== false;
            });
        }

        $render = $this->twig->render(
            'streamsSerie.html.twig',
            [
                'streams'    => $streams,
                'type'       => 'serie',
                'sort'       => $sort,
                'search'     => $search,
                'currentCat' => $category,
                'categories' => $categories,
                'catName'    => isset($categories[$category]) ? $categories[$category]->getName() : '',
            ]
        );

        echo $render;
    }
}
