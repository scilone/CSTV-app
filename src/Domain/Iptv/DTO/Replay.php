<?php

namespace App\Domain\Iptv\DTO;

use DateTimeImmutable;

class Replay
{
    /** @var int */
    private $id;

    /** @var int */
    private $epgId;

    /** @var string */
    private $title;

    /** @var string */
    private $lang;

    /** @var DateTimeImmutable */
    private $start;

    /** @var DateTimeImmutable */
    private $end;

    /** @var string */
    private $description;

    /** @var string */
    private $channelId;

    /** @var int */
    private $startTimestamp;

    /** @var int */
    private $stopTimestamp;

    /** @var int */
    private $nowPlaying;

    /** @var int */
    private $hasArchive;

    /** @var string */
    private $streamLink;

    public function __construct(
        int $id,
        int $epgId,
        string $title,
        string $lang,
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        string $description,
        string $channelId,
        int $startTimestamp,
        int $stopTimestamp,
        int $nowPlaying,
        int $hasArchive,
        string $streamLink
    ) {
        $this->id             = $id;
        $this->epgId          = $epgId;
        $this->title          = $title;
        $this->lang           = $lang;
        $this->start          = $start;
        $this->end            = $end;
        $this->description    = $description;
        $this->channelId      = $channelId;
        $this->startTimestamp = $startTimestamp;
        $this->stopTimestamp  = $stopTimestamp;
        $this->nowPlaying     = $nowPlaying;
        $this->hasArchive     = $hasArchive;
        $this->streamLink     = $streamLink;
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
    public function getStart(): DateTimeImmutable
    {
        return $this->start;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getEnd(): DateTimeImmutable
    {
        return $this->end;
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
    public function getStartTimestamp(): int
    {
        return $this->startTimestamp;
    }

    /**
     * @return int
     */
    public function getStopTimestamp(): int
    {
        return $this->stopTimestamp;
    }

    /**
     * @return int
     */
    public function getNowPlaying(): int
    {
        return $this->nowPlaying;
    }

    /**
     * @return int
     */
    public function getHasArchive(): int
    {
        return $this->hasArchive;
    }

    /**
     * @return string
     */
    public function getStreamLink(): string
    {
        return $this->streamLink;
    }
}
