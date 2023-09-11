<?php

namespace App\Repository;

use App\Entity\TransportEnCommun;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TransportEnCommun>
 *
 * @method TransportEnCommun|null find($id, $lockMode = null, $lockVersion = null)
 * @method TransportEnCommun|null findOneBy(array $criteria, array $orderBy = null)
 * @method TransportEnCommun[]    findAll()
 * @method TransportEnCommun[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransportEnCommunRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransportEnCommun::class);
    }

//    /**
//     * @return TransportEnCommun[] Returns an array of TransportEnCommun objects
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

//    public function findOneBySomeField($value): ?TransportEnCommun
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
