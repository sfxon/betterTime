<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;

/**
 * ProjectService
 */
class ProjectService
{
    private $doctrine;
    
    /**
     * __construct
     *
     * @param  ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function countAllProjects(User $user)
    {
        $repository = $this->doctrine->getRepository(Project::class);

        $count = $repository->createQueryBuilder('p')
            // Filter by some parameter if you want
            // ->where('a.published = 1')
            ->select('count(p.id)')
            ->where('p.user = :user')
            ->setParameter(':user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $count;
    }

    public function getProject($id)
    {
        $repository = $this->doctrine->getRepository(Project::class);
        $project = $repository->findOneBy(
            [ 'id' => $id ]
        );

        return $project;
    }

    public function getProjects($user, $limit, $page = 1, $sortBy = 'name', $sortOrder = 'ASC')
    {
        if ($limit == 0) {
            throw new \Exception('Limit should never be zero.');
        }

        if ($page < 1) {
            $page = 1;
        }

        // Calculate current page.
        $offset = ($page - 1) * $limit;

        $repository = $this->doctrine->getRepository(Project::class);
        $projects = $repository->findBy(
            [ 'user' => $user ], // Criteria
            [$sortBy => $sortOrder], // Sort-order
            $limit, // Limit
            $offset // Offset
        );

        return $projects;
    }

    public function loadById($projectId)
    {
        $repository = $this->doctrine->getRepository(Project::class);
        $project = $repository->findOneBy(
            [
                'id' => $projectId,
            ]
        );

        return $project;
    }

    /**
     * loadListByIds
     *
     * @param  array $idArray
     * @return array
     */
    public function loadListByIds(array $idArray): array
    {
        $repository = $this->doctrine->getRepository(Project::class);
        $retval = [];

        // FindBy will not work with an array of Uuids. So go with some queries for now.
        foreach ($idArray as $id) {
            $project = $repository->find($id);

            if (null !== $project) {
                $retval[] = $project;
            }
        }

        return $retval;
    }

    /**
     * searchByName
     *
     * @string  mixed $searchTerm
     * @int  mixed $maxResults
     */
    public function searchByName(string $searchTerm, int $maxResults = 10)
    {
        $repository = $this->doctrine->getRepository(Project::class);

        $result = $repository->createQueryBuilder('p')
            ->select('p')
            ->where('p.name LIKE :name')
            ->setParameter('name', '%' . $searchTerm . '%')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        if (!is_array($result)) {
            return null;
        }

        return $result;
    }
}
