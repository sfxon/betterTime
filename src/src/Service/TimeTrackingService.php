<?php

namespace App\Service;

use App\Entity\TimeTracking;

class TimeTrackingService
{
    private $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function endTimeTracking($timeTrackingId)
    {
        $entityManager = $this->doctrine->getManager();
        $timeTracking = $this->loadById($timeTrackingId);

        if (null === $timeTracking) {
            new \Exception('timeTracking Entry not found');
        }

        $now = new \DateTime();
        $timeTracking->setEndtime($now);

        $entityManager->persist($timeTracking);
        $entityManager->flush();
    }

    public function indexTimeTrackingResultsByProjectId($trackedTimes)
    {
        $retval = [];

        foreach ($trackedTimes as $tt) {
            $retval[$tt->getProject()->getId()->toRfc4122()] = $tt->getId();
        }

        return $retval;
    }

    public function loadAllNotEndedTimeTrackingEntries()
    {
        $repository = $this->doctrine->getRepository(TimeTracking::class);
        $trackedTimes = $repository->findBy(
            [
                'endtime' => null
            ]
        );

        return $trackedTimes;
    }

    public function loadById($timeTrackingId)
    {
        $repository = $this->doctrine->getRepository(TimeTracking::class);
        $trackedTime = $repository->findOneBy(
            [
                'id' => $timeTrackingId,
            ]
        );

        return $trackedTime;
    }

    public function loadOpenTimeTrackingForProject($project)
    {
        $repository = $this->doctrine->getRepository(TimeTracking::class);
        $trackedTime = $repository->findOneBy(
            [
                'project' => $project,
                'endtime' => null
            ]
        );

        return $trackedTime;
    }

    public function loadTimeTrackingsByProject($project)
    {
        $repository = $this->doctrine->getRepository(TimeTracking::class);
        $trackedTime = $repository->findBy(
            [
                'project' => $project,
            ],
            [
                'starttime' => 'DESC'
            ]
        );

        return $trackedTime;
    }

    public function startTimeTracking($project)
    {
        $entityManager = $this->doctrine->getManager();

        $now = new \DateTime();

        $timeTracking = new TimeTracking();
        $timeTracking->setProject($project);
        $timeTracking->setStarttime($now);
        $timeTracking->setUseOnInvoice(false);
        $entityManager->persist($timeTracking);
        $entityManager->flush();
    }

    public function update($timeTracking)
    {
        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($timeTracking);
        $entityManager->flush();
    }

    /**
     * Prepend an array of projects with the project,
     * that is assigned to the second parameter, a TimeTracking entry.
     *
     * @param  array $lastUsedProjects
     * @param  TimeTracking $timeTracking
     * @return array
     */
    public function prependLastUsedProjectsWithCurrentProject(array $lastUsedProjects, TimeTracking $timeTracking): array
    {
        $project = $timeTracking->getProject();

        if ($project === null) {
            return $lastUsedProjects;
        }

        $retval = [];
        $retval[] = [
            'id' => $project->getId(),
            'name' => $project->getName()
        ];

        // Make sure, the entry is not doubled in the array.
        // if it already exists.
        foreach($lastUsedProjects as $p) {
            $id = $project->getId();
            $name = $project->getName();

            if($p->getId() == $project->getId()) {
                continue;
            }

            $retval[] = $p;
        }
        
        return $retval;
    }
}
