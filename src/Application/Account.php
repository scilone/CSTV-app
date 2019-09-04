<?php

namespace App\Application;

use App\Config\Param;
use App\Config\SecretParam;
use App\Domain\Iptv\DTO\Category;
use App\Domain\Iptv\DTO\Live;
use App\Domain\Iptv\DTO\Movie;
use App\Domain\Iptv\DTO\MovieInfo;
use App\Domain\Iptv\DTO\Serie;
use App\Domain\Iptv\DTO\SerieEpisode;
use App\Domain\Iptv\DTO\SerieInfo;
use App\Domain\Iptv\DTO\SerieSeason;
use App\Domain\Iptv\DTO\Video;
use App\Domain\Iptv\XcodeApi;
use App\Domain\Repository\Account as AccountRepository;
use App\Infrastructure\CacheItem;
use App\Infrastructure\CurlOO;
use App\Infrastructure\SodiumDummies;
use App\Infrastructure\SuperglobalesOO;
use DateTimeImmutable;

class Account
{
    /**
     * @var SodiumDummies
     */
    private $sodium;

    /**
     * @var SuperglobalesOO
     */
    private $superglobales;

    /**
     * @var AccountRepository
     */
    private $repository;

    public function __construct(SodiumDummies $sodium, SuperglobalesOO $superglobales, AccountRepository $repository)
    {
        $this->sodium        = $sodium;
        $this->superglobales = $superglobales;
        $this->repository    = $repository;
    }

    public function create(string $username, string $password)
    {
        if ($username === '') {
            return false;
        }

        $data['nonce64'] = base64_encode($this->sodium->getNonce());

        $idUser = $this->repository->create($username, $password, $data);

        $this->setSessionBasicData(
            md5($password),
            $data['nonce64'],
            $username,
            $idUser
        );

        $this->generateCookiesAutolog($idUser, $password);

        return true;
    }

    public function connectFromCredentials(string $username, string $password): void
    {
        $userInfo = $this->repository->getFromUsername($username);

        if (password_verify($password, $userInfo['password']) === false) {
            return;
        }

        $this->setSessionBasicData(
            md5($password),
            $userInfo['data']['nonce64'],
            $username,
            $userInfo['id']
        );

        if (isset($userInfo['data'][Iptv::PREFIX . 'host'])) {
            $this->setSessionIptvData(
                $userInfo['data'][Iptv::PREFIX . 'username'],
                $userInfo['data'][Iptv::PREFIX . 'password'],
                $userInfo['data'][Iptv::PREFIX . 'host']
            );
        }

        $this->generateCookiesAutolog($userInfo['id'], $password);
    }

    public function connectFromCookie(): void
    {
        if ($this->superglobales->getCookie()->has('autologNonce64') === false
            || $this->superglobales->getCookie()->has('autologId') === false
        ) {
            return;
        }

        $nonce = base64_decode($this->superglobales->getCookie()->get('autologNonce64'));
        $key   = $this->sodium->generateCustomKey(SecretParam::SODIUM_KEY, 'secretbox');

        $userId = $this->sodium->openSecretBox(
            base64_decode($this->superglobales->getCookie()->get('autologId')),
            $nonce,
            $key
        );

        $userInfo = $this->getUserInfo($userId);

        $this->setSessionBasicData(
            $this->superglobales->getCookie()->get('autologSKey'),
            $userInfo['data']['nonce64'],
            $userInfo['username'],
            $userId
        );

        if (isset($userInfo['data'][Iptv::PREFIX . 'host'])) {
            $this->setSessionIptvData(
                $userInfo['data'][Iptv::PREFIX . 'username'],
                $userInfo['data'][Iptv::PREFIX . 'password'],
                $userInfo['data'][Iptv::PREFIX . 'host']
            );
        }
    }

    private function setSessionBasicData(
        string $sKey,
        string $nonce64,
        string $username,
        int $userId
    ): void {
        $this->superglobales->getSession()->set('SKey', $sKey);
        $this->superglobales->getSession()->set('nonce64', $nonce64);
        $this->superglobales->getSession()->set('username', $username);
        $this->superglobales->getSession()->set('userId', $userId);
    }

    private function setSessionIptvData(
        string $username,
        string $password,
        string $host
    ): void {
        $nonce = base64_decode($this->superglobales->getSession()->get('nonce64'));
        $key   = $this->sodium->generateCustomKey(
            $this->superglobales->getSession()->get('SKey'),
            'secretbox'
        );

        $this->superglobales->getSession()->set(
            Iptv::PREFIX . 'username',
            $this->sodium->openSecretBox(
                base64_decode($username),
                $nonce,
                $key
            )
        );
        $this->superglobales->getSession()->set(
            Iptv::PREFIX . 'password',
            $this->sodium->openSecretBox(
                base64_decode($password),
                $nonce,
                $key
            )
        );
        $this->superglobales->getSession()->set(
            Iptv::PREFIX . 'host',
            $this->sodium->openSecretBox(
                base64_decode($host),
                $nonce,
                $key
            )
        );
    }

    private function generateCookiesAutolog(int $userId, string $password)
    {
        $autologNonce = $this->sodium->getNonce();
        $key          = $this->sodium->generateCustomKey(SecretParam::SODIUM_KEY, 'secretbox');
        $autologId    = $this->sodium->createSecretBox($userId, $autologNonce, $key);

        $cookieDuration = time() + 3600 * 24 * 365;

        setcookie(
            'autologSKey',
            md5($password),
            $cookieDuration,
            Param:: BASE_URL_RELATIVE,
            false,
            true,
            true
        );
        setcookie(
            'autologNonce64',
            base64_encode($autologNonce),
            $cookieDuration,
            Param:: BASE_URL_RELATIVE,
            false,
            true,
            true
        );
        setcookie(
            'autologId',
            base64_encode($autologId),
            $cookieDuration,
            Param:: BASE_URL_RELATIVE,
            false,
            true,
            true,
        );
    }

    public function setIptvInfo(string $username, string $password, string $host): void
    {
        $nonce     = base64_decode($this->superglobales->getSession()->get('nonce64'));
        $key       = $this->sodium->generateCustomKey(
            $this->superglobales->getSession()->get('SKey'),
            'secretbox'
        );

        $data = [
            Iptv::PREFIX . 'username' => base64_encode($this->sodium->createSecretBox($username, $nonce, $key)),
            Iptv::PREFIX . 'password' => base64_encode($this->sodium->createSecretBox($password, $nonce, $key)),
            Iptv::PREFIX . 'host'     => base64_encode($this->sodium->createSecretBox($host, $nonce, $key)),
        ];

        $this->repository->addDataForUser(
            $this->superglobales->getSession()->get('userId'),
            $data
        );

        $this->setSessionIptvData(
            $data[Iptv::PREFIX . 'username'],
            $data[Iptv::PREFIX . 'password'],
            $data[Iptv::PREFIX . 'host']
        );
    }

    private function getUserInfo(int $userId): array
    {
        return $this->repository->getFromId($userId);
    }
}