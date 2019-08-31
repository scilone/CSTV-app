<?php

namespace App\Controller;

use App\Application\Iptv;
use App\Application\Twig;
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

        echo $this->twig->render('streamsMovieInfo.html.twig', ['movie' => $movie]);
    }

    public function serieInfo(string $id): void
    {
        $serie = $this->iptv->getSerieInfo($id);

        echo $this->twig->render('streamsSerieInfo.html.twig', ['serie' => $serie]);
    }

    public function serie(string $category, int $sort = 0, string $search = ''): void
    {
        $search = urldecode($search);

        $filter = [];
        if (is_numeric($category)) {
            $filter['cat'] = $category;
        }

        if ($search !== '') {
            $filter['search'] = $search;
        }

        $streams = $this->iptv->getSerieStreams($filter, $sort);
        $categories = $this->iptv->getSerieCategories();

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
