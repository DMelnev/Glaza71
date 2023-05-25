<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Author;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllSortedByUpdate()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->orderBy('a.updatedAt', 'DESC')
            ->leftJoin('a.author', 'u')
            ->addSelect('u')
            ->leftJoin('a.comments', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult();
    }

    public function findAllSortedByUpdateNotPublished()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id > 0')
            ->orderBy('a.updatedAt', 'DESC')
            ->leftJoin('a.author', 'u')
            ->addSelect('u')
            ->leftJoin('a.comments', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult();
    }

    public function findLast(int $num)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.publishedAt IS NOT NULL')
            ->orderBy('a.updatedAt', 'DESC')
            ->setMaxResults($num)
            ->leftJoin('a.author', 'u')
            ->addSelect('u')
            ->leftJoin('a.comments', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult();
    }

    public function search(string $search)
    {
        if( empty($search)) return [];
        $search = '%' . trim($search) . '%';
//        dd($search);
        return $this->createQueryBuilder('ar')
            ->leftJoin('ar.author', 'au')
            ->addSelect('au')
            ->andWhere('
                au.firstName LIKE :search
                OR au.surname LIKE :search
                OR au.patronymic LIKE :search
                OR ar.description LIKE :search
                OR ar.title LIKE :search
                OR ar.text LIKE :search
           ')
            ->andWhere('ar.publishedAt IS NOT NULL')
            ->setParameter('search', $search)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
