<?php

namespace App\Domain\Iptv\DTO;

use DateTimeImmutable;

class Live
{
    /**
     * @var int
     */
    private $num;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $streamType;

    /**
     * @var int
     */
    private $streamId;

    /**
     * @var string
     */
    private $streamIcon;

    /**
     * @var int
     */
    private $epgChannelId;

    /**
     * @var DateTimeImmutable
     */
    private $added;

    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var string
     */
    private $customSid;

    /**
     * @var int
     */
    private $tvArchive;

    /**
     * @var string
     */
    private $directSource;

    /**
     * @var int
     */
    private $tvArchiveDuration;

    /**
     * @var string
     */
    private $streamLink;

    /**
     * Live constructor.
     *
     * @param int               $num
     * @param string            $name
     * @param string            $streamType
     * @param int               $streamId
     * @param string            $streamIcon
     * @param int               $epgChannelId
     * @param DateTimeImmutable $added
     * @param int               $categoryId
     * @param string            $customSid
     * @param int               $tvArchive
     * @param string            $directSource
     * @param int               $tvArchiveDuration
     * @param string            $streamLink
     */
    public function __construct(
        int $num,
        string $name,
        string $streamType,
        int $streamId,
        string $streamIcon,
        int $epgChannelId,
        DateTimeImmutable $added,
        int $categoryId,
        string $customSid,
        int $tvArchive,
        string $directSource,
        int $tvArchiveDuration,
        string $streamLink
    ) {
        $this->num               = $num;
        $this->name              = $name;
        $this->streamType        = $streamType;
        $this->streamId          = $streamId;
        $this->streamIcon        = $streamIcon;
        $this->epgChannelId      = $epgChannelId;
        $this->added             = $added;
        $this->categoryId        = $categoryId;
        $this->customSid         = $customSid;
        $this->tvArchive         = $tvArchive;
        $this->directSource      = $directSource;
        $this->tvArchiveDuration = $tvArchiveDuration;
        $this->streamLink        = $streamLink;
    }

    /**
     * @return int
     */
    public function getNum(): int
    {
        return $this->num;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getStreamType(): string
    {
        return $this->streamType;
    }

    /**
     * @return int
     */
    public function getStreamId(): int
    {
        return $this->streamId;
    }

    /**
     * @return string
     */
    public function getStreamIcon(): string
    {
        return $this->streamIcon;
    }

    /**
     * @return int
     */
    public function getEpgChannelId(): int
    {
        return $this->epgChannelId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getAdded(): DateTimeImmutable
    {
        return $this->added;
    }

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    /**
     * @return string
     */
    public function getCustomSid(): string
    {
        return $this->customSid;
    }

    /**
     * @return int
     */
    public function getTvArchive(): int
    {
        return $this->tvArchive;
    }

    /**
     * @return string
     */
    public function getDirectSource(): string
    {
        return $this->directSource;
    }

    /**
     * @return int
     */
    public function getTvArchiveDuration(): int
    {
        return $this->tvArchiveDuration;
    }

    /**
     * @return string
     */
    public function getStreamLink(): string
    {
        return $this->streamLink;
    }
}
