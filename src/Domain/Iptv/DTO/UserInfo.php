<?php

namespace App\Domain\Iptv\DTO;

use DateTimeImmutable;

class UserInfo
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string */
    private $message;

    /** @var int */
    private $auth;

    /** @var string */
    private $status;

    /** @var DateTimeImmutable */
    private $expDate;

    /** @var bool */
    private $isTrial;

    /** @var int */
    private $activeCons;

    /** @var DateTimeImmutable */
    private $createdAt;

    /** @var int */
    private $maxConnections;

    /** @var array */
    private $allowedOutputFormats;

    /**
     * UserInfo constructor.
     *
     * @param string            $username
     * @param string            $password
     * @param string            $message
     * @param int               $auth
     * @param string            $status
     * @param DateTimeImmutable $expDate
     * @param bool              $isTrial
     * @param int               $activeCons
     * @param DateTimeImmutable $createdAt
     * @param int               $maxConnections
     * @param array             $allowedOutputFormats
     */
    public function __construct(
        string $username,
        string $password,
        string $message,
        int $auth,
        string $status,
        DateTimeImmutable $expDate,
        bool $isTrial,
        int $activeCons,
        DateTimeImmutable $createdAt,
        int $maxConnections,
        array $allowedOutputFormats
    ) {
        $this->username             = $username;
        $this->password             = $password;
        $this->message              = $message;
        $this->auth                 = $auth;
        $this->status               = $status;
        $this->expDate              = $expDate;
        $this->isTrial              = $isTrial;
        $this->activeCons           = $activeCons;
        $this->createdAt            = $createdAt;
        $this->maxConnections       = $maxConnections;
        $this->allowedOutputFormats = $allowedOutputFormats;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getAuth(): int
    {
        return $this->auth;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getExpDate(): DateTimeImmutable
    {
        return $this->expDate;
    }

    /**
     * @return bool
     */
    public function isTrial(): bool
    {
        return $this->isTrial;
    }

    /**
     * @return int
     */
    public function getActiveCons(): int
    {
        return $this->activeCons;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getMaxConnections(): int
    {
        return $this->maxConnections;
    }

    /**
     * @return array
     */
    public function getAllowedOutputFormats(): array
    {
        return $this->allowedOutputFormats;
    }
}
