<?php

namespace App\Controller;

use App\Application\Iptv;
use App\Config\Param;
use App\Infrastructure\SuperglobalesOO;

class SecurityController
{
    /**
     * @var SuperglobalesOO
     */
    private $superglobales;

    /**
     * AssetController constructor.
     *
     * @param SuperglobalesOO $superglobales
     */
    public function __construct(SuperglobalesOO $superglobales)
    {
        $this->superglobales = $superglobales;

        if ($this->superglobales->getSession()->has(Iptv::PREFIX . 'host') === false) {
            $usernameKey = Iptv::PREFIX . 'username';
            $username = $this->getStoreData($usernameKey) !== null
                ? $this->getStoreData($usernameKey)
                :'PoK9vGq5ei';

            $passwordKey = Iptv::PREFIX . 'password';
            $password = $this->getStoreData($passwordKey) !== null
                ? $this->getStoreData($passwordKey)
                :'Bl1dcXYK1S';

            $hostKey = Iptv::PREFIX . 'host';
            $host = $this->getStoreData($hostKey) !== null
                ? $this->getStoreData($hostKey)
                :'http://netflexx.org:8000';

            setcookie($usernameKey, $username, time() + 3600 * 48);
            setcookie($passwordKey, $password, time() + 3600 * 48);
            setcookie($hostKey, $host, time() + 3600 * 48);

            $this->superglobales->getSession()
                ->set($usernameKey, $username)
                ->set($passwordKey, $password)
                ->set($hostKey, $host);
        }
    }

    private function getStoreData(string $key): ?string
    {
        if ($this->superglobales->getQuery()->has($key)) {
            return $this->superglobales->getQuery()->get($key);
        } elseif ($this->superglobales->getCookie()->has($key)) {
            return $this->superglobales->getCookie()->get($key);
        } elseif ($this->superglobales->getSession()->has($key)) {
            return $this->superglobales->getSession()->get($key);
        }

        return null;
    }
}
