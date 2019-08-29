<?php

namespace App\Domain\Iptv\DTO;

class Video
{
    /**
     * @var string
     */
    private $codec;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * Video constructor.
     *
     * @param string $codec
     * @param int    $width
     * @param int    $height
     */
    public function __construct(string $codec, int $width, int $height)
    {
        $this->codec  = $codec;
        $this->width  = $width;
        $this->height = $height;
    }

    /**
     * @return string
     */
    public function getCodec(): string
    {
        return $this->codec;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }
}
