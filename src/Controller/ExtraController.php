<?php

namespace App\Controller;

use App\Application\Iptv;
use App\Application\Twig;
use App\Infrastructure\SuperglobalesOO;

class ExtraController
{
    /**
     * @var Twig
     */
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }


    public function speedtest(): void
    {
        echo $this->twig->render('extraSpeedtest.html.twig');
    }
}
