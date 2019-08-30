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

        //Diamond
        $this->superglobales->getSession()
            ->set(Iptv::PREFIX . 'username', 'eAV7mQ8oNt')
            ->set(Iptv::PREFIX . 'password', 'AK0WeU7u2Z')
            ->set(Iptv::PREFIX . 'host', 'http://netflexx.org:8000');

        //Gold
        /*$this->superglobales->getSession()
            ->set(Iptv::PREFIX . 'username', '45165901520581')
            ->set(Iptv::PREFIX . 'password', '45165901520581')
            ->set(Iptv::PREFIX . 'host', 'http://iptv.smartgotv.com:8080');*/
    }


    public function main(): void
    {
        echo $this->twig->render('homeMain.html.twig');
    }
}
