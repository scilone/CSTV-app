<?php

namespace App\Domain\Iptv\DTO;

class Category
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $parentId;

    public function __construct(int $id, string $name, int $parentId)
    {
        $this->id       = $id;
        $this->name     = $name;
        $this->parentId = $parentId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }
}
