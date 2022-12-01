<?php

namespace App\Entity;

use App\Repository\InternalStatEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: InternalStatEntityRepository::class)]
class InternalStatEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[ORM\Column(length: 1024)]
    private ?string $technicalName = null;

    #[ORM\OneToMany(targetEntity: InternalStat::class, mappedBy: 'track_keeping_entity')]
    private $internalStats;

    public function __construct()
    {
        $this->internalStats = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getInternalStats(): Collection
    {
        return $this->internalStats;
    }
}
