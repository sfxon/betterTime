<?php

namespace App\Service;

use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;

class ProjectService {
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine) {
        $this->doctrine = $doctrine;
    }
    
    public function countAllProjects() {
        $repository = $this->doctrine->getRepository(Project::class);

        $count = $repository->createQueryBuilder('p')
            // Filter by some parameter if you want
            // ->where('a.published = 1')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return $count;
    }

    /*
     * @deprecated
     */
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

    public function getProjects($limit, $page = 1, $sortBy = 'name', $sortOrder = 'ASC') {
        if($limit == 0) {
            throw new \Exception('Limit should never be zero.');
        }

        if($page < 1) {
            $page = 1;
        }

        // Calculate current page.
        $offset = ($page - 1) * $limit;
        
        $repository = $this->doctrine->getRepository(Project::class);
        $projects = $repository->findBy(
            [], // Empty criteria, gets all results.
            [$sortBy => $sortOrder], // Sort-order
            $limit, // Limit
            $offset // Offset
        );

        return $projects;
    }

    public function loadById($projectId) {
        $repository = $this->doctrine->getRepository(Project::class);
        $project = $repository->findOneBy(
            [
                'id' => $projectId,
            ]
        );

        return $project;
    }

    public function searchByName($searchTerm, $maxResults = 10) {
        $repository = $this->doctrine->getRepository(Project::class);

        $result = $repository->createQueryBuilder('p')
            ->select('p')
            ->where('p.name LIKE :name')
            ->setParameter('name', '%' . $searchTerm . '%')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        if(!is_array($result)) {
            return null;
        }

        return $result;
    }
}