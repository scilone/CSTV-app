<?php

namespace App\Domain\Iptv\DTO;

use DateTimeImmutable;

class SerieSeason
{
    /**
     * @var DateTimeImmutable
     */
    private $airDate;

    /**
     * @var int
     */
    private $episodeCount;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $overview;

    /**
     * @var int
     */
    private $seasonNumber;

    /**
     * @var string
     */
    private $cover;

    /**
     * @var string
     */
    private $coverBig;

    /**
     * SerieSeason constructor.
     *
     * @param DateTimeImmutable $airDate
     * @param int               $episodeCount
     * @param int               $id
     * @param string            $name
     * @param string            $overview
     * @param int               $seasonNumber
     * @param string            $cover
     * @param string            $coverBig
     */
    public function __construct(
        DateTimeImmutable $airDate,
        int $episodeCount,
        int $id,
        string $name,
        string $overview,
        int $seasonNumber,
        string $cover,
        string $coverBig
    ) {
        $this->airDate      = $airDate;
        $this->episodeCount = $episodeCount;
        $this->id           = $id;
        $this->name         = $name;
        $this->overview     = $overview;
        $this->seasonNumber = $seasonNumber;
        $this->cover        = $cover;
        $this->coverBig     = $coverBig;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getAirDate(): DateTimeImmutable
    {
        return $this->airDate;
    }

    /**
     * @return int
     */
    public function getEpisodeCount(): int
    {
        return $this->episodeCount;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
    public function getOverview(): string
    {
        return $this->overview;
    }

    /**
     * @return int
     */
    public function getSeasonNumber(): int
    {
        return $this->seasonNumber;
    }

    /**
     * @return string
     */
    public function getCover(): string
    {
        return $this->cover;
    }

    /**
     * @return string
     */
    public function getCoverBig(): string
    {
        return $this->coverBig;
    }
}
