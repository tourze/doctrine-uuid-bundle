<?php

namespace Tourze\DoctrineUuidBundle\Tests\Integration\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;

#[ORM\Entity]
class TestEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[UuidV1Column]
    private string $id = '';

    #[ORM\Column(type: 'string', length: 36, nullable: true)]
    #[UuidV4Column]
    private ?string $uuid = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
