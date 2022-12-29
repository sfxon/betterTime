<?php

namespace App\Entity;

use App\Repository\ConfigValueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ConfigValueRepository::class)]
class ConfigValue
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'uuid')]
    private $configDefinitionId = null;

    #[ORM\Column(length: 16)]
    private ?string $level = null;

    #[ORM\Column(type: 'uuid', nullable: true)]
    private $foreignId = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $value = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getConfigDefinitionId(): Uuid
    {
        return $this->configDefinitionId;
    }

    public function setConfigDefinitionId($configDefinitionId): self
    {
        $this->configDefinitionId = $configDefinitionId;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getForeignId(): ?Uuid
    {
        return $this->foreignId;
    }

    public function setForeignId($foreignId): self
    {
        $this->foreignId = $foreignId;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
