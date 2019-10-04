<?php

namespace App\Controller;

use App\Application\Iptv;
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
     * @var Iptv
     */
    private $iptv;

    /**
     * HomeController constructor.
     *
     * @param Twig            $twig
     * @param SuperglobalesOO $superglobales
     * @param Iptv            $iptv
     */
    public function __construct(Twig $twig, SuperglobalesOO $superglobales, Iptv $iptv)
    {
        $this->twig          = $twig;
        $this->superglobales = $superglobales;
        $this->iptv          = $iptv;

        parent::__construct($superglobales);
    }

    public function main(): void
    {
        $userInfo = $this->iptv->getUserInfo();

        $userExpired = mb_strtolower($userInfo->getStatus()) === 'expired';

        if ($userExpired === false && $this->superglobales->getCookie()->has('redirect')) {
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
                'hasLiveFavorites' => !empty($this->superglobales->getSession()->get('favorites')['live']),
                'hasMovieFavorites' => !empty($this->superglobales->getSession()->get('favorites')['movie']),
                'hasSerieFavorites' => !empty($this->superglobales->getSession()->get('favorites')['serie']),
                'connectedAs'       => $this->superglobales->getSession()->get('username'),
                'hasExpired'        => $userExpired,
                'userInfo'          => $userInfo,
            ]
        );
    }
}
