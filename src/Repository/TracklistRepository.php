<?php

namespace App\Repository;

use App\Entity\Tracklist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tracklist>
 *
 * @method Tracklist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tracklist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tracklist[]    findAll()
 * @method Tracklist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TracklistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tracklist::class);
    }

    public function save(Tracklist $tracklist): void
    {
        $this->getEntityManager()->persist($tracklist);
    }

    public function hardSave(Tracklist $tracklist): void
    {
        $this->getEntityManager()->flush();
    }

//    /**
//     * @return Tracklist[] Returns an array of Tracklist objects
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

//    public function findOneBySomeField($value): ?Tracklist
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
