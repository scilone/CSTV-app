<?php

namespace App\Controller;

use App\Application\Account;
use App\Application\Iptv;
use App\Application\Twig;
use App\Infrastructure\CacheRaw;
use App\Infrastructure\SuperglobalesOO;

class CategoryController extends SecurityController
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

    public function live(): void
    {
        $cacheName = md5($this->superglobales->getSession()->get(Iptv::PREFIX . 'host')) . '_category_live';
        $cache = $this->cacheRaw->get($cacheName, '1 week');

        if ($cache !== null) {
            echo $cache;

            return;
        }

        $categories = $this->iptv->getLiveCategories();

        $hiddenCategories = [];
        if (isset($this->superglobales->getSession()->get('hiddenCategories')['live'])) {
            foreach ($categories as $keyCat => $cat) {
                if (isset($this->superglobales->getSession()->get('hiddenCategories')['live'][$cat->getId()])) {
                    $hiddenCategories[] = $cat;
                    unset($categories[$keyCat]);
                }
            }
        }

        echo $this->twig->render(
            'category.html.twig',
            [
                'categories'       => $categories,
                'type'             => 'live',
                'hiddenCategories' => $hiddenCategories,
            ]
        );
    }

    public function movie(): void
    {
        $cacheName = md5($this->superglobales->getSession()->get(Iptv::PREFIX . 'host')) . '_category_movie';
        $cache = $this->cacheRaw->get($cacheName, '1 week');

        if ($cache !== null) {
            echo $cache;

            return;
        }

        $categories = $this->iptv->getMovieCategories();

        $render = $this->twig->render(
            'category.html.twig',
            [
                'categories'  => $categories,
                'type'        => 'movie',
            ]
        );

        //$this->cacheRaw->set($cacheName, $render);

        echo $render;
    }

    public function serie(): void
    {
        $cacheName = md5($this->superglobales->getSession()->get(Iptv::PREFIX . 'host')) . '_category_serie';
        $cache = $this->cacheRaw->get($cacheName, '1 week');

        if ($cache !== null) {
            echo $cache;

            return;
        }

        $categories = $this->iptv->getSerieCategories();

        $render = $this->twig->render(
            'category.html.twig',
            [
                'categories' => $categories,
                'type'       => 'serie',
            ]
        );

        //$this->cacheRaw->set($cacheName, $render);

        echo $render;
    }

    public function hide(string $type, int $id)
    {
        $this->account->hideCategory($type, $id);

        echo '<script>window.history.go(-1);</script>';
    }

    public function unhide(string $type, int $id)
    {
        $this->account->unhideCategory($type, $id);

        echo '<script>window.history.go(-1);</script>';
    }
}
