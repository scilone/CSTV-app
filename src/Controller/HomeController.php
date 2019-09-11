<?php

namespace App\Controller;

use App\Application\Twig;
use App\Config\Param;
use App\Infrastructure\SuperglobalesOO;

class HomeController extends SecurityController
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

        parent::__construct($superglobales);
    }

    public function main(): void
    {
        if ($this->superglobales->getCookie()->has('redirect')) {
            setcookie(
                'redirect',
                '',
                0,
                Param:: BASE_URL_RELATIVE,
                false,
                true,
                true
            );

            header('Location: ' . $this->superglobales->getCookie()->get('redirect'));
        }


        echo $this->twig->render(
            'homeMain.html.twig',
            [
                'hasMovieFavorites' => !empty($this->superglobales->getSession()->get('favorites')['movie']),
                'hasSerieFavorites' => !empty($this->superglobales->getSession()->get('favorites')['serie']),
                'connectedAs'       => $this->superglobales->getSession()->get('username')
            ]
        );
    }
}
