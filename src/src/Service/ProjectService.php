<?php

namespace App\Service;

use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;

class ProjectService {
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    /*
     * @deprecated
     */
    public function getAllProjects() {
        $repository = $this->doctrine->getRepository(Project::class);
        $projects = $repository->findAll();

        return $projects;
    }

    public function getProjects($limit) {
        $repository = $this->doctrine->getRepository(Project::class);
        $projects = $repository->findBy(
            [], // Empty criteria, gets all results.
            ['name' => 'ASC'], // Sort-order
            $limit, // Limit
            0 // Offset
        );

        return $projects;
    }

    public function getProject($id) {
        $repository = $this->doctrine->getRepository(Project::class);
        $project = $repository->findOneBy(
            [ 'id' => $id ]
        );

        return $project;
    }
}