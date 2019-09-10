<?php

namespace App\Controller;

use App\Application\Account;
use App\Application\Iptv;
use App\Application\Twig;
use App\Config\Param;
use App\Domain\Iptv\DTO\Live;
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
     * @var Account
     */
    private $account;

    public function __construct(
        Twig $twig,
        Iptv $iptv,
        CacheRaw $cacheRaw,
        SuperglobalesOO $superglobales,
        Account $account
    ) {
        $this->twig          = $twig;
        $this->iptv          = $iptv;
        $this->cacheRaw      = $cacheRaw;
        $this->superglobales = $superglobales;
        $this->account       = $account;

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

    public function liveInfo(string $streamName): void
    {
        $streamName = base64_decode($streamName);

        $streams  = $this->iptv->getLiveStreamsByName($streamName);

        /** @var Live $refStream */
        $refStream = current($streams);

        $shortEpg = $this->iptv->getShortEPG($refStream->getStreamId());
        $name     = $refStream->getName();
        $img      = $refStream->getStreamIcon();

        $streamsSorted = [];
        if (isset($streams['4K'])) {
            $streamsSorted['4K'] = $streams['4K'];
        }
        if (isset($streams['FHD'])) {
            $streamsSorted['FHD'] = $streams['FHD'];
        }
        if (isset($streams['HD'])) {
            $streamsSorted['HD'] = $streams['HD'];
        }
        if (isset($streams['HEVC'])) {
            $streamsSorted['HEVC'] = $streams['HEVC'];
        }
        if (isset($streams['SD'])) {
            $streamsSorted['SD'] = $streams['SD'];
        }
        $streamsSorted += $streams;

        $render = $this->twig->render(
            'streamsLiveInfo.html.twig',
            [
                'streams'  => $streamsSorted,
                'type'     => 'live',
                'shortEpg' => $shortEpg,
                'name'     => $name,
                'img'      => $img,
            ]
        );

        echo $render;
    }

    public function live(string $category): void
    {
        $filter = [];
        if (is_numeric($category)) {
            $filter['cat'] = $category;
        }

        $streams    = $this->iptv->getLiveStreams($filter);
        $categories = $this->iptv->getLiveCategories();

        $catName = isset($categories[$category]) ? $categories[$category]->getName() : '';

        $hiddenCategories = [];
        if (isset($this->superglobales->getSession()->get('hiddenCategories')['live'])) {
            foreach ($categories as $keyCat => $cat) {
                if (isset($this->superglobales->getSession()->get('hiddenCategories')['live'][$cat->getId()])) {
                    $hiddenCategories[] = $cat;
                    unset($categories[$keyCat]);
                }
            }
        }

        $render = $this->twig->render(
            'streamsLive.html.twig',
            [
                'streams'          => $streams,
                'catName'          => $catName,
                'type'             => 'live',
                'currentCat'       => $category,
                'hiddenCategories' => $hiddenCategories,
                'isHidden'         => isset($categories[$category]) ? false : true,
            ]
        );

        echo $render;
    }

    private function cleanSearch(string $search)
    {
        $str = htmlentities($search, ENT_NOQUOTES, 'utf-8');
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);
        $str = preg_replace('#[^\w]#', '', $str);

        return $str;
    }

    public function movie(string $category, int $sort = 0, string $search = ''): void
    {
        $search = urldecode($search);

        $filter = [];
        if (is_numeric($category)) {
            $filter['cat'] = $category;
        }

        $streams = $this->iptv->getMovieStreams($filter, $sort);
        $categories = $this->iptv->getMovieCategories();

        $catName = isset($categories[$category]) ? $categories[$category]->getName() : '';

        $hiddenCategories = [];
        if (isset($this->superglobales->getSession()->get('hiddenCategories')['movie'])) {
            foreach ($categories as $keyCat => $cat) {
                if (isset($this->superglobales->getSession()->get('hiddenCategories')['movie'][$cat->getId()])) {
                    $hiddenCategories[] = $cat;
                    unset($categories[$keyCat]);
                }
            }
        }

        if ($category === 'favorites') {
            $favorites = $this->superglobales->getSession()->get('favorites')['movie'];
            $streams = array_filter($streams, function ($var) use ($favorites) {
                return isset($favorites[$var->getStreamId()]);
            });
        }

        if ($search !== '') {
            $searchCleaned = $this->cleanSearch($search);
            $streams = array_filter($streams, function ($var) use ($searchCleaned) {
                return stripos($this->cleanSearch($var->getName()), $searchCleaned) !== false;
            });
        }

        $render = $this->twig->render(
            'streamsMovie.html.twig',
            [
                'streams'          => $streams,
                'type'             => 'movie',
                'sort'             => $sort,
                'search'           => $search,
                'currentCat'       => $category,
                'categories'       => $categories,
                'hiddenCategories' => $hiddenCategories,
                'catName'          => $catName,
                'isHidden'         => isset($categories[$category]) ? false : true,
                'streamView'       => $this->superglobales->getSession()->get('flaggedStreams')['movie']
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
                'streamView' => $this->superglobales->getSession()->get('flaggedStreams')['movie'],
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
                'streamView' => $this->superglobales->getSession()->get('flaggedStreams')['serie'],
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

        $streams = $this->iptv->getSerieStreams($filter, $sort);
        $categories = $this->iptv->getSerieCategories();

        $catName = isset($categories[$category]) ? $categories[$category]->getName() : '';

        $hiddenCategories = [];
        if (isset($this->superglobales->getSession()->get('hiddenCategories')['serie'])) {
            foreach ($categories as $keyCat => $cat) {
                if (isset($this->superglobales->getSession()->get('hiddenCategories')['serie'][$cat->getId()])) {
                    $hiddenCategories[] = $cat;
                    unset($categories[$keyCat]);
                }
            }
        }

        if ($category === 'favorites') {
            $favorites = $this->superglobales->getSession()->get('favorites')['serie'];
            $streams = array_filter($streams, function ($var) use ($favorites) {
                return isset($favorites[$var->getSerieId()]);
            });
        }

        if ($search !== '') {
            $searchCleaned = $this->cleanSearch($search);
            $streams = array_filter($streams, function ($var) use ($searchCleaned) {
                return stripos($this->cleanSearch($var->getName()), $searchCleaned) !== false;
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
                'hiddenCategories' => $hiddenCategories,
                'catName'    => $catName,
                'isHidden'   => isset($categories[$category]) ? false : true,
            ]
        );

        echo $render;
    }

    public function flagAsView(string $type, int $id, string $url)
    {
        $this->account->flagStreamAsView($type, $id);

        header('Location: ' . base64_decode($url));
    }
}
