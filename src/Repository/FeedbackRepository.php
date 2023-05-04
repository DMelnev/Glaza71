<?php

namespace App\Repository;

use App\Entity\Feedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feedback>
 *
 * @method Feedback|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feedback|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feedback[]    findAll()
 * @method Feedback[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedbackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feedback::class);
    }

    public function add(Feedback $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Feedback $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findWithSearchQuery(?string $search, ?string $userId): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.author', 'a')
            ->addSelect('a');

        if ($search && $search != '') {
            $qb
                ->andWhere('c.text LIKE :search 
                OR a.firstname LIKE :search 
                OR a.surname LIKE :search 
                OR a.patronymic LIKE :search
                OR c.other_author LIKE :search')
                ->setParameter('search', "%$search%");
        }
        if ($userId && $userId != '') {
            $qb
                ->andWhere('a.id = :id')
                ->setParameter('id', $userId);
        }

        return $qb
            ->orderBy('c.createdAt', 'DESC');
    }

    public function findLastCurrentAuthor(int $author)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.author = :author')
            ->setParameter('author', $author)
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults(1)
            ->leftJoin('c.author', 'a')
            ->addSelect('c')
            ->getQuery()
            ->getResult();
    }

    public function findAllCurrentPage(int $page, int $viewer)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.author = :viewer OR f.publishedAt IS NOT NULL')
            ->andWhere('f.mainPage = :page')
            ->setParameter('page', $page)
            ->setParameter('viewer', $viewer)
            ->orderBy('f.createdAt', 'DESC')
            ->leftJoin('f.author', 'a')
            ->addSelect('a')
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Feedback[] Returns an array of Feedback objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Feedback
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
