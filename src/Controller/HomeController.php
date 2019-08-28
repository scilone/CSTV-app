<?php

namespace App\Controller;

use App\Application\Twig;

class HomeController
{
    /**
     * @var Twig
     */
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function main(): void
    {
        echo $this->twig->render('homeMain.html.twig');
    }
}
