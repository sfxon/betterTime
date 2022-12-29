<?php

namespace App\Entity;

use App\Repository\ConfigDefinitionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ConfigDefinitionRepository::class)]
class ConfigDefinition
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $technicalName = null;

    #[ORM\Column]
    private array $levels = [];

    #[ORM\Column(length: 4096, nullable: true)]
    private ?string $description = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTechnicalName(): ?string
    {
        return $this->technicalName;
    }

    public function setTechnicalName(string $technicalName): self
    {
        $this->technicalName = $technicalName;

        return $this;
    }

    public function getLevels(): array
    {
        return $this->levels;
    }

    public function setLevels(array $levels): self
    {
        $this->levels = $levels;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
