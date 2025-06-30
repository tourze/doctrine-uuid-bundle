<?php

namespace Tourze\DoctrineUuidBundle\Tests\Integration\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;

#[ORM\Entity]
#[ORM\Table(name: 'test_entity', options: ['comment' => '测试实体表'])]
class TestEntity implements \Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 36, options: ['comment' => 'UUID v1 主键'])]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[UuidV1Column]
    private string $id = '';

    #[ORM\Column(type: Types::STRING, length: 36, nullable: true, options: ['comment' => 'UUID v4 字段'])]
    #[UuidV4Column]
    private ?string $uuid = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '名称'])]
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

    public function __toString(): string
    {
        return $this->name !== '' ? $this->name : $this->id;
    }
}
