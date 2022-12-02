<?php

namespace App\Entity;

use App\Repository\InternalStatRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: InternalStatRepository::class)]
class InternalStat
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: InternalStatEntity::class, inversedBy: 'track_keepings')]
    private ?InternalStatEntity $internalStatEntity = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $entry = null;

    #[ORM\Column]
    private ?int $count = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastUsage = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getInternalStatEntity(): ?InternalStatEntity
    {
        return $this->internalStatEntity;
    }

    public function setInternalStatEntity(InternalStatEntity $internalStatEntity): self
    {
        $this->internalStatEntity = $internalStatEntity;

        return $this;
    }

    public function getEntry(): ?Uuid
    {
        return $this->entry;
    }

    public function setEntry(Uuid $entry): self
    {
        $this->entry = $entry;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getLastUsage(): ?\DateTimeInterface
    {
        return $this->lastUsage;
    }

    public function setLastUsage(\DateTimeInterface $lastUsage): self
    {
        $this->lastUsage = $lastUsage;

        return $this;
    }
}
