<?php

namespace App\Domain\Iptv\DTO;

use DateTimeImmutable;

class MovieInfo
{
    /**
     * @var string
     */
    private $image;

    /**
     * @var array
     */
    private $backdropPath;

    /**
     * @var int
     */
    private $durationSecondes;

    /**
     * @var string
     */
    private $dureationFormatted;

    /**
     * @var Video
     */
    private $video;

    /**
     * @var int
     */
    private $bitrate;

    /**
     * @var string
     */
    private $youtubeTrailer;

    /**
     * @var string
     */
    private $genre;

    /**
     * @var string
     */
    private $plot;

    /**
     * @var string
     */
    private $cast;

    /**
     * @var float
     */
    private $rating;

    /**
     * @var string
     */
    private $director;

    /**
     * @var DateTimeImmutable
     */
    private $releaseDate;

    /**
     * @var int
     */
    private $streamId;

    /**
     * @var string
     */
    private $name;

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
     * @var int
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
     * @param string            $image
     * @param array             $backdropPath
     * @param int               $durationSecondes
     * @param string            $dureationFormatted
     * @param Video             $video
     * @param int               $bitrate
     * @param string            $youtubeTrailer
     * @param string            $genre
     * @param string            $plot
     * @param string            $cast
     * @param float             $rating
     * @param string            $director
     * @param DateTimeImmutable $releaseDate
     * @param int               $streamId
     * @param string            $name
     * @param DateTimeImmutable $added
     * @param int               $categoryId
     * @param string            $containerExtension
     * @param int               $customSid
     * @param string            $directSource
     * @param string            $streamLink
     */
    public function __construct(
        string $image,
        array $backdropPath,
        int $durationSecondes,
        string $dureationFormatted,
        Video $video,
        int $bitrate,
        string $youtubeTrailer,
        string $genre,
        string $plot,
        string $cast,
        float $rating,
        string $director,
        DateTimeImmutable $releaseDate,
        int $streamId,
        string $name,
        DateTimeImmutable $added,
        int $categoryId,
        string $containerExtension,
        int $customSid,
        string $directSource,
        string $streamLink
    ) {
        $this->image              = $image;
        $this->backdropPath       = $backdropPath;
        $this->durationSecondes   = $durationSecondes;
        $this->dureationFormatted = $dureationFormatted;
        $this->video              = $video;
        $this->bitrate            = $bitrate;
        $this->youtubeTrailer     = $youtubeTrailer;
        $this->genre              = $genre;
        $this->plot               = $plot;
        $this->cast               = $cast;
        $this->rating             = $rating;
        $this->director           = $director;
        $this->releaseDate        = $releaseDate;
        $this->streamId           = $streamId;
        $this->name               = $name;
        $this->added              = $added;
        $this->categoryId         = $categoryId;
        $this->containerExtension = $containerExtension;
        $this->customSid          = $customSid;
        $this->directSource       = $directSource;
        $this->streamLink         = $streamLink;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @return array
     */
    public function getBackdropPath(): array
    {
        return $this->backdropPath;
    }

    /**
     * @return int
     */
    public function getDurationSecondes(): int
    {
        return $this->durationSecondes;
    }

    /**
     * @return string
     */
    public function getDureationFormatted(): string
    {
        return $this->dureationFormatted;
    }

    /**
     * @return Video
     */
    public function getVideo(): Video
    {
        return $this->video;
    }

    /**
     * @return int
     */
    public function getBitrate(): int
    {
        return $this->bitrate;
    }

    /**
     * @return string
     */
    public function getYoutubeTrailer(): string
    {
        return $this->youtubeTrailer;
    }

    /**
     * @return string
     */
    public function getGenre(): string
    {
        return $this->genre;
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
     * @return float
     */
    public function getRating(): float
    {
        return $this->rating;
    }

    /**
     * @return string
     */
    public function getDirector(): string
    {
        return $this->director;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getReleaseDate(): DateTimeImmutable
    {
        return $this->releaseDate;
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
    public function getName(): string
    {
        return $this->name;
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
     * @return int
     */
    public function getCustomSid(): int
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
