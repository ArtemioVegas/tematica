<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="empl")
 * @ORM\Entity()
 */
class Employer
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=50, name="id")
     */
    private string $id;

    /**
     * @ORM\Column(type="string", length=50, name="parent_id", nullable=true)
     */
    private ?string $parentId = null;

    public function __construct(string $id, string $parentId)
    {
        $this->id = $id;
        $this->parentId = $parentId ?: null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }
}
