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

    public function iptv()
    {
        if ($this->superglobales->getPost()->has('username')) {
            $this->account->setIptvInfo(
                $this->superglobales->getPost()->get('username'),
                $this->superglobales->getPost()->get('password'),
                $this->superglobales->getPost()->get('host'),
            );

            $this->redirectToHome();
        }

        echo $this->twig->render(
            'accountIptv.html.twig',
            [
                'username' => $this->superglobales->getSession()->get(Iptv::PREFIX . 'username'),
                'password' => $this->superglobales->getSession()->get(Iptv::PREFIX . 'password'),
                'host'     => $this->superglobales->getSession()->get(Iptv::PREFIX . 'host'),
            ]
        );
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
        session_start();
        session_unset();
        session_destroy();

        foreach ($this->superglobales->getCookie()->all() as $cookieName => $cookieValue) {
            setcookie($cookieName, '', 0, Param::BASE_URL_RELATIVE);
        }

        $this->redirectToHome();
    }

    public function addfavorite(string $type, $id)
    {
        $this->account->addFavorite($type, $id);

        echo '<script>window.history.go(-1);</script>';
    }

    public function removeFavorite(string $type, $id)
    {
        $this->account->removeFavorite($type, $id);

        echo '<script>window.history.go(-1);</script>';
    }
}
