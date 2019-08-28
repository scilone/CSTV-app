<?php

namespace App\Domain\Iptv\DTO;

use DateTimeImmutable;

class Movie
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
     * @var float
     */
    private $rating;

    /**
     * @var float
     */
    private $rating5based;

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
    private $containerExtension;

    /**
     * @var string
     */
    private $customSid;

    /**
     * @var string
     */
    private $directSource;

    /**
     * @var string
     */
    private $streamLink;

    /**
     * Movie constructor.
     *
     * @param int               $num
     * @param string            $name
     * @param string            $streamType
     * @param int               $streamId
     * @param string            $streamIcon
     * @param float             $rating
     * @param float             $rating5based
     * @param DateTimeImmutable $added
     * @param int               $categoryId
     * @param string            $containerExtension
     * @param string            $customSid
     * @param string            $directSource
     * @param string            $streamLink
     */
    public function __construct(
        int $num,
        string $name,
        string $streamType,
        int $streamId,
        string $streamIcon,
        float $rating,
        float $rating5based,
        DateTimeImmutable $added,
        int $categoryId,
        string $containerExtension,
        string $customSid,
        string $directSource,
        string $streamLink
    ) {
        $this->num                = $num;
        $this->name               = $name;
        $this->streamType         = $streamType;
        $this->streamId           = $streamId;
        $this->streamIcon         = $streamIcon;
        $this->rating             = $rating;
        $this->rating5based       = $rating5based;
        $this->added              = $added;
        $this->categoryId         = $categoryId;
        $this->containerExtension = $containerExtension;
        $this->customSid          = $customSid;
        $this->directSource       = $directSource;
        $this->streamLink         = $streamLink;
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
     * @return string
     */
    public function getRating(): float
    {
        return $this->rating;
    }

    /**
     * @return float
     */
    public function getRating5based(): float
    {
        return $this->rating5based;
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
    public function getContainerExtension(): string
    {
        return $this->containerExtension;
    }

    /**
     * @return string
     */
    public function getCustomSid(): string
    {
        return $this->customSid;
    }

    /**
     * @return string
     */
    public function getDirectSource(): string
    {
        return $this->directSource;
    }

    /**
     * @return string
     */
    public function getStreamLink(): string
    {
        return $this->streamLink;
    }
}
