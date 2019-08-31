<?php

namespace App\Domain\Iptv\DTO;

use DateTimeImmutable;

class SerieEpisode
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $episodeNum;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $containerExtension;

    /**
     * @var int
     */
    private $customSid;

    /**
     * @var DateTimeImmutable
     */
    private $added;

    /**
     * @var string
     */
    private $season;

    /**
     * @var string
     */
    private $directSource;

    /**
     * @var Video
     */
    private $video;

    /**
     * @var string
     */
    private $movieImage;

    /**
     * @var string
     */
    private $plot;

    /**
     * @var DateTimeImmutable
     */
    private $releasedate;

    /**
     * @var string
     */
    private $rating;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $durationSecondes;

    /**
     * @var string
     */
    private $dureationFormatted;

    /**
     * @var int
     */
    private $bitrate;

    /**
     * @var string
     */
    private $streamLink;

    /**
     * SerieEpisode constructor.
     *
     * @param int               $id
     * @param int               $episodeNum
     * @param string            $title
     * @param string            $containerExtension
     * @param int               $customSid
     * @param DateTimeImmutable $added
     * @param string            $season
     * @param string            $directSource
     * @param Video             $video
     * @param string            $movieImage
     * @param string            $plot
     * @param DateTimeImmutable $releasedate
     * @param string            $rating
     * @param string            $name
     * @param int               $durationSecondes
     * @param string            $dureationFormatted
     * @param int               $bitrate
     * @param string            $streamLink
     */
    public function __construct(
        int $id,
        int $episodeNum,
        string $title,
        string $containerExtension,
        int $customSid,
        DateTimeImmutable $added,
        string $season,
        string $directSource,
        Video $video,
        string $movieImage,
        string $plot,
        DateTimeImmutable $releasedate,
        string $rating,
        string $name,
        int $durationSecondes,
        string $dureationFormatted,
        int $bitrate,
        string $streamLink
    ) {
        $this->id                 = $id;
        $this->episodeNum         = $episodeNum;
        $this->title              = $title;
        $this->containerExtension = $containerExtension;
        $this->customSid          = $customSid;
        $this->added              = $added;
        $this->season             = $season;
        $this->directSource       = $directSource;
        $this->video              = $video;
        $this->movieImage         = $movieImage;
        $this->plot               = $plot;
        $this->releasedate        = $releasedate;
        $this->rating             = $rating;
        $this->name               = $name;
        $this->durationSecondes   = $durationSecondes;
        $this->dureationFormatted = $dureationFormatted;
        $this->bitrate            = $bitrate;
        $this->streamLink         = $streamLink;
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
    public function getEpisodeNum(): int
    {
        return $this->episodeNum;
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
     * @return DateTimeImmutable
     */
    public function getAdded(): DateTimeImmutable
    {
        return $this->added;
    }

    /**
     * @return string
     */
    public function getSeason(): string
    {
        return $this->season;
    }

    /**
     * @return string
     */
    public function getDirectSource(): string
    {
        return $this->directSource;
    }

    /**
     * @return Video
     */
    public function getVideo(): Video
    {
        return $this->video;
    }

    /**
     * @return string
     */
    public function getMovieImage(): string
    {
        return $this->movieImage;
    }

    /**
     * @return string
     */
    public function getPlot(): string
    {
        return $this->plot;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getReleasedate(): DateTimeImmutable
    {
        return $this->releasedate;
    }

    /**
     * @return string
     */
    public function getRating(): string
    {
        return $this->rating;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
     * @return int
     */
    public function getBitrate(): int
    {
        return $this->bitrate;
    }

    /**
     * @return string
     */
    public function getStreamLink(): string
    {
        return $this->streamLink;
    }
}
