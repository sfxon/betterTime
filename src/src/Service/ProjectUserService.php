<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * ProjectUserService
 */
class ProjectUserService
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
        
    /**
     * countAllProjects
     *
     * @param  User $user
     * @return int
     */
    public function countAllProjects(User $user): int
    {
        $repository = $this->doctrine->getRepository(Project::class);

        $count = $repository->createQueryBuilder('p')
            ->select('count(p.id)')
            ->where('p.user = :user')
            ->setParameter(':user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        return $count;
    }
    
    /**
     * getProject
     *
     * @param  Uuid $id
     * @param  User $user
     * @return ?Project
     */
    public function getProject(Uuid $id, User $user): ?Project
    {
        $repository = $this->doctrine->getRepository(Project::class);
        $project = $repository->findOneBy([
            'id' => $id,
            'user' => $user
        ]);

        return $project;
    }
    
    /**
     * getProjects
     *
     * @param  User $user
     * @param  int $limit
     * @param  int $page
     * @param  string $sortBy
     * @param  string $sortOrder
     * @return array
     */
    public function getProjects(
        User $user,
        int $limit,
        int $page = 1,
        $sortBy = 'name',
        $sortOrder = 'ASC')
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
    
    /**
     * loadById
     *
     * @param  Uuid $projectId
     * @param  User $user
     * @return ?Project
     */
    public function loadById(Uuid $projectId, User $user): ?Project
    {
        $repository = $this->doctrine->getRepository(Project::class);
        $project = $repository->findOneBy(
            [
                'id' => $projectId,
                'user' => $user
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
    public function loadListByIds(array $idArray, User $user): array
    {
        $repository = $this->doctrine->getRepository(Project::class);
        $retval = [];

        // FindBy will not work with an array of Uuids. So go with some queries for now.
        foreach ($idArray as $id) {
            $project = $repository->findBy([
                'id' => $id,
                'user' => $user
            ]);

            if (null !== $project) {
                $retval[] = $project;
            }
        }

        return $retval;
    }

    /**
     * searchByName
     *
     * @param string  $searchTerm
     * @param int $maxResults
     * @return ?array
     */
    public function searchByName(string $searchTerm, User $user, int $maxResults = 10): ?array
    {
        $repository = $this->doctrine->getRepository(Project::class);

        $result = $repository->createQueryBuilder('p')
            ->select('p')
            ->where('p.name LIKE :name AND p.user = :user')
            ->setParameter('name', '%' . $searchTerm . '%')
            ->setParameter(':user', $user)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        if (!is_array($result)) {
            return null;
        }

        return $result;
    }
}
