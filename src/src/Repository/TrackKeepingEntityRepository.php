<?php

namespace App\Repository;

use App\Entity\TrackKeepingEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TrackKeepingEntity>
 *
 * @method TrackKeepingEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrackKeepingEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrackKeepingEntity[]    findAll()
 * @method TrackKeepingEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackKeepingEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrackKeepingEntity::class);
    }

    public function save(TrackKeepingEntity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TrackKeepingEntity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TrackKeepingEntity[] Returns an array of TrackKeepingEntity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TrackKeepingEntity
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
