<?php

namespace App\Service;

use App\Entity\Project;

class ProjectService {
    private $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    public function getAllProjects() {
        $repository = $this->doctrine->getRepository(Project::class);
        $projects = $repository->findAll();

        return $projects;
    }
}