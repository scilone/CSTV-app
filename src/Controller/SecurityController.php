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
            header('Location: ' . Param::BASE_URL_ABSOLUTE . Param::HOME_URL_RELATIVE);
            exit;
        }
    }
}
