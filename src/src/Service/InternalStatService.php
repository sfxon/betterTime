<?php

namespace App\Service;

use App\Entity\InternalStat;
use App\Entity\InternalStatEntity;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

class InternalStatService {
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function trackEntityUsage(String $technicalName, Uuid $entry) {
        $entityManager = $this->doctrine->getManager();
        
        // Load InternalStatEntity by id
        $internalStatEntity = $this->loadInternalStatEntityByTechnicalName($technicalName);

        if(null === $internalStatEntity) {
            throw new \Exception('No InternalStatEntity found, that has a technicalName of "' . $technicalName . '"');
        }

        // Load InternalStat-Entry by InternalStatEntity id
        $internalStat = $this->loadInternalStatEntryByInternalStatEntityId($internalStatEntity, $entry);

        // Update InternalStat-Entry
        $data = [];

        if(null === $internalStat) {
            $internalStat = new InternalStat();
            $internalStat->setInternalStatEntity($internalStatEntity);
            $internalStat->setEntry($entry);
            $internalStat->setCount(1);
            $internalStat->setLastUsage(new \DateTime());
        } else {
            $internalStat->setCount(1 + $internalStat->getCount());
            $internalStat->setLastUsage(new \DateTime());
        }

        $entityManager->persist($internalStat);
        $entityManager->flush();
    }

    private function loadInternalStatEntityByTechnicalName($technicalName) {
        $repository = $this->doctrine->getRepository(InternalStatEntity::class);
        $internalStatEntity = $repository->findOneBy(
            [
                'technicalName' => $technicalName,
            ]
        );

        return $internalStatEntity;
    }

    private function loadInternalStatEntryByInternalStatEntityId(InternalStatEntity $internalStatEntity, Uuid $entry): ?InternalStat
    {
        $repository = $this->doctrine->getRepository(InternalStat::class);
        $internalStat = $repository->findOneBy(
            [
                'internalStatEntity' => $internalStatEntity,
                'entry' => $entry
            ]
        );

        return $internalStat;
    }
}