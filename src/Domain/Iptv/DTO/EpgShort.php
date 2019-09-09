<?php

namespace App\Domain\Iptv\DTO;

use DateTimeImmutable;

class EpgShort
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $epgId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $lang;

    /**
     * @var DateTimeImmutable
     */
    private $dateStart;

    /**
     * @var DateTimeImmutable
     */
    private $dateEnd;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $channelId;

    /**
     * @var int
     */
    private $tsStart;

    /**
     * @var int
     */
    private $tsEnd;

    /**
     * EpgShort constructor.
     *
     * @param int               $id
     * @param int               $epgId
     * @param string            $title
     * @param string            $lang
     * @param DateTimeImmutable $dateStart
     * @param DateTimeImmutable $dateEnd
     * @param string            $description
     * @param string            $channelId
     * @param int               $tsStart
     * @param int               $tsEnd
     */
    public function __construct(
        int $id,
        int $epgId,
        string $title,
        string $lang,
        DateTimeImmutable $dateStart,
        DateTimeImmutable $dateEnd,
        string $description,
        string $channelId,
        int $tsStart,
        int $tsEnd
    ) {
        $this->id          = $id;
        $this->epgId       = $epgId;
        $this->title       = $title;
        $this->lang        = $lang;
        $this->dateStart   = $dateStart;
        $this->dateEnd     = $dateEnd;
        $this->description = $description;
        $this->channelId   = $channelId;
        $this->tsStart     = $tsStart;
        $this->tsEnd       = $tsEnd;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getEpgId(): int
    {
        return $this->epgId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDateStart(): DateTimeImmutable
    {
        return $this->dateStart;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDateEnd(): DateTimeImmutable
    {
        return $this->dateEnd;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getChannelId(): string
    {
        return $this->channelId;
    }

    /**
     * @return int
     */
    public function getTsStart(): int
    {
        return $this->tsStart;
    }

    /**
     * @return int
     */
    public function getTsEnd(): int
    {
        return $this->tsEnd;
    }
}
