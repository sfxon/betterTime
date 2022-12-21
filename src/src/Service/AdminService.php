<?php

namespace App\Service;

use App\Entity\Admin;
use Doctrine\Persistence\ManagerRegistry;

class AdminService
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function countAllAdmins()
    {
        $repository = $this->doctrine->getRepository(Admin::class);

        $count = $repository->createQueryBuilder('p')
            // Filter by some parameter if you want
            // ->where('a.published = 1')
            ->select('count(p.id)')
            ->getQuery()
            ->getSingleScalarResult();

        return $count;
    }

    public function getAdmin($id)
    {
        $repository = $this->doctrine->getRepository(Admin::class);
        $admin = $repository->findOneBy(
            [ 'id' => $id ]
        );

        return $admin;
    }

    public function getAdmins($limit, $page = 1, $sortBy = 'email', $sortOrder = 'ASC')
    {
        if ($limit == 0) {
            throw new \Exception('Limit should never be zero.');
        }

        if ($page < 1) {
            $page = 1;
        }

        // Calculate current page.
        $offset = ($page - 1) * $limit;

        $repository = $this->doctrine->getRepository(Admin::class);
        $admins = $repository->findBy(
            [], // Empty criteria, gets all results.
            [$sortBy => $sortOrder], // Sort-order
            $limit, // Limit
            $offset // Offset
        );

        return $admins;
    }

    public function loadById($adminId)
    {
        $repository = $this->doctrine->getRepository(Admin::class);
        $admin = $repository->findOneBy(
            [
                'id' => $adminId,
            ]
        );

        return $admin;
    }

    /**
     * loadListByIds
     *
     * @param  array $idArray
     * @return array
     */
    public function loadListByIds(array $idArray): array
    {
        $repository = $this->doctrine->getRepository(Admin::class);
        $retval = [];

        // FindBy will not work with an array of Uuids. So go with some queries for now.
        foreach ($idArray as $id) {
            $admin = $repository->find($id);

            if (null !== $admin) {
                $retval[] = $admin;
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
        $repository = $this->doctrine->getRepository(Admin::class);

        $result = $repository->createQueryBuilder('p')
            ->select('p')
            ->where('p.email LIKE :email')
            ->setParameter('email', '%' . $searchTerm . '%')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        if (!is_array($result)) {
            return null;
        }

        return $result;
    }
}
