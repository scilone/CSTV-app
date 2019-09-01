<?php

namespace App\Controller;

use App\Application\Iptv;
use App\Application\Twig;
use App\Infrastructure\SuperglobalesOO;

class HomeController
{
    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var SuperglobalesOO
     */
    private $superglobales;

    /**
     * HomeController constructor.
     *
     * @param Twig            $twig
     * @param SuperglobalesOO $superglobales
     */
    public function __construct(Twig $twig, SuperglobalesOO $superglobales)
    {
        $this->twig          = $twig;
        $this->superglobales = $superglobales;
    }


    public function main(): void
    {
        echo $this->twig->render('homeMain.html.twig');
    }
}
