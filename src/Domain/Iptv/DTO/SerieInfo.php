<?php

namespace App\Domain\Iptv\DTO;

use DateTimeImmutable;

class SerieInfo
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $cover;

    /**
     * @var string
     */
    private $plot;

    /**
     * @var string
     */
    private $cast;

    /**
     * @var string
     */
    private $director;

    /**
     * @var string
     */
    private $genre;

    /**
     * @var DateTimeImmutable
     */
    private $releaseDate;

    /**
     * @var DateTimeImmutable
     */
    private $lastModified;

    /**
     * @var float
     */
    private $rating;

    /**
     * @var float
     */
    private $rating5based;

    /**
     * @var array
     */
    private $backdropPath;

    /**
     * @var string
     */
    private $youtubeTrailer;

    /**
     * @var int
     */
    private $episodeRunTime;

    /**
     * @var int
     */
    private $categoryId;

    /**
     * @var SerieSeason[]
     */
    private $seasons;

    /**
     * @var SerieEpisode[]
     */
    private $episodes;

    /**
     * SerieInfo constructor.
     *
     * @param string            $id
     * @param string            $name
     * @param string            $cover
     * @param string            $plot
     * @param string            $cast
     * @param string            $director
     * @param string            $genre
     * @param DateTimeImmutable $releaseDate
     * @param DateTimeImmutable $lastModified
     * @param float             $rating
     * @param float             $rating5based
     * @param array             $backdropPath
     * @param string            $youtubeTrailer
     * @param int               $episodeRunTime
     * @param int               $categoryId
     * @param SerieSeason[]     $seasons
     * @param SerieEpisode[]    $episodes
     */
    public function __construct(
        string $id,
        string $name,
        string $cover,
        string $plot,
        string $cast,
        string $director,
        string $genre,
        DateTimeImmutable $releaseDate,
        DateTimeImmutable $lastModified,
        float $rating,
        float $rating5based,
        array $backdropPath,
        string $youtubeTrailer,
        int $episodeRunTime,
        int $categoryId,
        array $seasons,
        array $episodes
    ) {
        $this->id             = $id;
        $this->name           = $name;
        $this->cover          = $cover;
        $this->plot           = $plot;
        $this->cast           = $cast;
        $this->director       = $director;
        $this->genre          = $genre;
        $this->releaseDate    = $releaseDate;
        $this->lastModified   = $lastModified;
        $this->rating         = $rating;
        $this->rating5based   = $rating5based;
        $this->backdropPath   = $backdropPath;
        $this->youtubeTrailer = $youtubeTrailer;
        $this->episodeRunTime = $episodeRunTime;
        $this->categoryId     = $categoryId;
        $this->seasons        = $seasons;
        $this->episodes       = $episodes;
    }

    /**
     * @return string
     */
    public function getId(): string
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
    public function getCover(): string
    {
        return $this->cover;
    }

    /**
     * @return string
     */
    public function getPlot(): string
    {
        return $this->plot;
    }

    /**
     * @return string
     */
    public function getCast(): string
    {
        return $this->cast;
    }

    /**
     * @return string
     */
    public function getDirector(): string
    {
        return $this->director;
    }

    /**
     * @return string
     */
    public function getGenre(): string
    {
        return $this->genre;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getReleaseDate(): DateTimeImmutable
    {
        return $this->releaseDate;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getLastModified(): DateTimeImmutable
    {
        return $this->lastModified;
    }

    /**
     * @return float
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
     * @return array
     */
    public function getBackdropPath(): array
    {
        return $this->backdropPath;
    }

    /**
     * @return string
     */
    public function getYoutubeTrailer(): string
    {
        return $this->youtubeTrailer;
    }

    /**
     * @return int
     */
    public function getEpisodeRunTime(): int
    {
        return $this->episodeRunTime;
    }

    /**
     * @return int
     */
    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    /**
     * @return SerieSeason[]
     */
    public function getSeasons(): array
    {
        return $this->seasons;
    }

    /**
     * @return SerieEpisode[]
     */
    public function getEpisodes(): array
    {
        return $this->episodes;
    }
}
