<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\TimeTracking;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'project:item']),
        new GetCollection(normalizationContext: ['groups' => 'project:list'])
    ],
    order: ['name' => 'ASC'],
    paginationEnabled: true,
    paginationItemsPerPage: 10,
)]
class Project
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['project:list', 'project:item'])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['project:list', 'project:item'])]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: TimeTracking::class, mappedBy: 'project')]
    private $timeTrackings;

    public function __construct()
    {
        $this->timeTrackings = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTimeTrackings(): Collection
    {
        return $this->timeTrackings;
    }
}
