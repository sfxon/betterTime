<?php

namespace App\Service;

use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;

class ProjectService {
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }

    public function getAllProjects() {
        $repository = $this->doctrine->getRepository(Project::class);
        $projects = $repository->findAll();

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