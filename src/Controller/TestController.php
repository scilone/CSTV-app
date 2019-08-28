<?php

namespace App\Controller;

use App\Application\Twig;
use App\Application\CacheRaw;

class TestController
{
    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var CacheRaw
     */
    private $cacheRaw;

    /**
     * @var string
     */
    private $helloWorldMsg;

    public function __construct(Twig $twig, CacheRaw $cacheRaw, string $helloWorldMsg)
    {
        $this->twig          = $twig;
        $this->cacheRaw      = $cacheRaw;
        $this->helloWorldMsg = $helloWorldMsg;
    }

    public function helloWorld(): void
    {
        echo $this->twig->render('testHelloWorld.html.twig', ['msg' => $this->helloWorldMsg]);
    }

    public function cache(): void
    {
        var_dump($this->cacheRaw->getCache('test'));
        $this->cacheRaw->setCache('test', time());
    }

    public function stream()
    {
        echo '
        <a href="vlc://http://netflexx.org:8000/1Vjfv6!P!N/yDD38Z5ObO/11705">LIVE</a>
        <a href="vlc://http://netflexx.org:8000/movie/1Vjfv6!P!N/yDD38Z5ObO/184204.mkv">MOVIE</a>
        <a href="vlc://http://netflexx.org:8000/series/1Vjfv6!P!N/yDD38Z5ObO/91785.mkv">SERIE</a>
        ';
    }
}
