<?php

namespace App\Controller;

use App\Application\Account;
use App\Application\Iptv;
use App\Application\Twig;
use App\Config\Param;
use App\Infrastructure\SodiumDummies;
use App\Infrastructure\SuperglobalesOO;

class AccountController
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
     * @var Account
     */
    private $account;

    public function __construct(Twig $twig, SuperglobalesOO $superglobales, Account $account)
    {
        $this->twig          = $twig;
        $this->superglobales = $superglobales;
        $this->account       = $account;
    }

    public function info()
    {
        if ($this->superglobales->getPost()->has('username')) {
            $this->account->setIptvInfo(
                $this->superglobales->getPost()->get('username'),
                $this->superglobales->getPost()->get('password'),
                $this->superglobales->getPost()->get('host'),
            );

            $this->redirectToHome();
        }

        echo $this->twig->render('accountInfo.html.twig');
    }

    public function register()
    {
        if ($this->superglobales->getPost()->has('username')) {
            $this->account->create(
                $this->superglobales->getPost()->get('username'),
                $this->superglobales->getPost()->get('password')
            );

            $this->redirectToHome();
        }

        echo $this->twig->render('accountRegister.html.twig');
    }

    public function log()
    {
        if ($this->superglobales->getPost()->has('username')) {
            $this->account->connectFromCredentials(
                $this->superglobales->getPost()->get('username'),
                $this->superglobales->getPost()->get('password')
            );

            $this->redirectToHome();
        }

        echo $this->twig->render('accountLog.html.twig');
    }

    private function redirectToHome(): void
    {
        header('Location: ' . Param::BASE_URL_ABSOLUTE . Param::HOME_URL_RELATIVE);
        exit;
    }

    public function autolog()
    {
        $this->account->connectFromCookie();

        $this->redirectToHome();
    }

    public function logout(): void
    {
        session_destroy();

        setcookie(Iptv::PREFIX . 'username', '', 0, Param::BASE_URL_RELATIVE);
        setcookie(Iptv::PREFIX . 'password', '', 0, Param::BASE_URL_RELATIVE);
        setcookie(Iptv::PREFIX . 'host', '', 0, Param::BASE_URL_RELATIVE);

        $this->redirectToHome();
    }

    public function addfavorite(string $type, int $id)
    {
        $this->account->addFavorite($type, $id);

        echo '<script>window.history.go(-1);</script>';
    }

    public function removeFavorite(string $type, int $id)
    {
        $this->account->removeFavorite($type, $id);

        echo '<script>window.history.go(-1);</script>';
    }
}
