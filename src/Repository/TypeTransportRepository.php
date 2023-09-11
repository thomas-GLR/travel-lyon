<?php

namespace App\Repository;

use App\Entity\TypeTransport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeTransport>
 *
 * @method TypeTransport|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeTransport|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeTransport[]    findAll()
 * @method TypeTransport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeTransportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeTransport::class);
    }

//    /**
//     * @return TypeTransport[] Returns an array of TypeTransport objects
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

//    public function findOneBySomeField($value): ?TypeTransport
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
