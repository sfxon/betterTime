<?php

namespace App\Service;

use App\Entity\TimeTracking;

class TimeTrackingService {
    private $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    public function startTimeTracking($projectId)
     {
         // Entity erstellen
         $entityManager = $this->doctrine->getManager();

         $now = new \DateTime();

         $timeTracking = new TimeTracking();
         $timeTracking->setProjectId($projectId);
         $timeTracking->setStarttime($now);
         $timeTracking->setUseOnInvoice(false);
         $entityManager->persist($timeTracking);
         $entityManager->flush();
    }

    public function loadOpenTimeTrackingForProject($projectId) {
        $repository = $this->doctrine->getRepository(TimeTracking::class);
        $trackedTime = $repository->findOneBy(
            [
                'projectId' => $projectId,
                'endtime' => NULL
            ]
        );

        return $trackedTime;
    }

    public function loadAllNotEndedTimeTrackingEntries() {
        $repository = $this->doctrine->getRepository(TimeTracking::class);
        $trackedTimes = $repository->findBy(
            [
                'endtime' => NULL
            ]
        );

        return $trackedTimes;
    }

    public function indexTimeTrackingResultsByProjectId($trackedTimes) {
        $retval = [];

        foreach($trackedTimes as $tt) {
            $retval[(string)$tt->getProjectId()] = $tt;
        }

        return $retval;
    }

    public function loadById($timeTrackingId) {
        $repository = $this->doctrine->getRepository(TimeTracking::class);
        $trackedTime = $repository->findOneBy(
            [
                'id' => $timeTrackingId,
            ]
        );

        return $trackedTime;
    }

    public function endTimeTracking($timeTrackingId) {
        $entityManager = $this->doctrine->getManager();
        $timeTracking = $this->loadById($timeTrackingId);

        if(null === $timeTracking) {
            new \Exception('timeTracking Entry not found');
        }

        $now = new \DateTime();
        $timeTracking->setEndtime($now);

        $entityManager->persist($timeTracking);
        $entityManager->flush();
    }

    public function loadTimeTrackingsByProjectId($projectId) {
        $repository = $this->doctrine->getRepository(TimeTracking::class);
        $trackedTime = $repository->findBy(
            [
                'projectId' => $projectId,
            ],
            [
                'starttime' => 'asc'
            ]
        );

        return $trackedTime;
    }

    public function update($timeTracking) {
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($timeTracking);
        $entityManager->flush();
    }
}