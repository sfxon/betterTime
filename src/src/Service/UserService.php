<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * UserService
 */
class UserService
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Get user by email address.
     * Since the email address is unique in the database,
     * this should return one user or null.
     *
     * @param  string $email
     * @return ?User
     */
    public function loadByEmail(string $email): ?User
    {
        $repository = $this->doctrine->getRepository(User::class);
        $user = $repository->findOneBy(
            [ 'email' => $email ]
        );

        return $user;
    }

    /**
     * Load by id.
     *
     * @param  Uuid $userId
     * @return ?User
     */
    public function loadById(Uuid $userId): ?User
    {
        $repository = $this->doctrine->getRepository(User::class);
        $user = $repository->findOneBy(
            [
                'id' => $userId,
            ]
        );

        return $user;
    }
}
