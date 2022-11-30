<?php

namespace App\Service;

use App\Entity\TrackKeeping;
use App\Entity\TrackKeepingEntity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class TrackKeepingService {
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function trackEntityUsage(String $technicalName, Uuid $entry) {
        $entityManager = $this->doctrine->getManager();
        
        // Load TrackKeepingEntity by id
        $trackKeepingEntity = $this->loadTrackKeepingEntityByTechnicalName($technicalName);

        if(null === $trackKeepingEntity) {
            throw new \Exception('No TrackKeepingEntity found, that has a technicalName of "' . $technicalName . '"');
        }

        // Load TrackKeeping-Entry by TrackKeepingEntity id
        $trackKeeping = $this->loadTrackKeepingEntryByTrackKeepingEntityId($trackKeepingEntity, $entry);

        // Update TrackKeeping-Entry
        $data = [];

        if(null === $trackKeeping) {
            $trackKeeping = new TrackKeeping();
            $trackKeeping->setTrackKeepingEntity($trackKeepingEntity);
            $trackKeeping->setEntry($entry);
            $trackKeeping->setCount(1);
            $trackKeeping->setLastUsage(new \DateTime());
        } else {
            $trackKeeping->setCount(1 + $trackKeeping->getCount());
            $trackKeeping->setLastUsage(new \DateTime());
        }

        $entityManager->persist($trackKeeping);
        $entityManager->flush();
    }

    private function loadTrackKeepingEntityByTechnicalName($technicalName) {
        $repository = $this->doctrine->getRepository(TrackKeepingEntity::class);
        $trackKeepingEntity = $repository->findOneBy(
            [
                'technicalName' => $technicalName,
            ]
        );

        return $trackKeepingEntity;
    }

    private function loadTrackKeepingEntryByTrackKeepingEntityId(TrackKeepingEntity $trackKeepingEntity, Uuid $entry): ?TrackKeeping
    {
        $repository = $this->doctrine->getRepository(TrackKeeping::class);
        $trackKeeping = $repository->findOneBy(
            [
                'trackKeepingEntity' => $trackKeepingEntity,
                'entry' => $entry
            ]
        );

        return $trackKeeping;
    }
}