<?php

namespace App\Controller;

use App\Application\Iptv;
use App\Application\Twig;
use App\Config\Param;
use App\Infrastructure\SodiumDummies;
use App\Infrastructure\SuperglobalesOO;

class AccountController extends SecurityController
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
     * @var SodiumDummies
     */
    private $sodium;

    public function __construct(Twig $twig, SuperglobalesOO $superglobales, SodiumDummies $sodium)
    {
        $this->twig          = $twig;
        $this->superglobales = $superglobales;
        $this->sodium        = $sodium;

        parent::__construct($superglobales);
    }

    public function register()
    {
        if ($this->superglobales->getPost()->has('username')) {
            var_dump(1);
            exit;
        }

        echo $this->twig->render('accountRegister.html.twig');
    }

    public function logout(): void
    {
        session_destroy();

        setcookie(Iptv::PREFIX . 'username', '', 0, Param::BASE_URL_RELATIVE);
        setcookie(Iptv::PREFIX . 'password', '', 0, Param::BASE_URL_RELATIVE);
        setcookie(Iptv::PREFIX . 'host', '', 0, Param::BASE_URL_RELATIVE);

        //exit;

        header('Location: ' . Param::BASE_URL_ABSOLUTE . Param::HOME_URL_RELATIVE);
    }
}
