<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 *
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function add(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Comment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByArticleId(int $id, int $viewer)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.article = :id')
            ->andWhere('c.author = :viewer OR c.publishedAt IS NOT NULL')
            ->setParameter('id', $id)
            ->setParameter('viewer', $viewer)
            ->orderBy('c.createdAt', 'DESC')
            ->leftJoin('c.author', 'a')
            ->addSelect('a')
            ->leftJoin('c.article', 'r')
            ->addSelect('r')
            ->getQuery()
            ->getResult();
    }

    public function findLast(int $num)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.publishedAt IS NOT NULL')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($num)
            ->leftJoin('c.author', 'a')
            ->addSelect('c')
            ->leftJoin('c.article', 'r')
            ->addSelect('r')
            ->getQuery()
            ->getResult();
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
            ->leftJoin('c.article', 'r')
            ->addSelect('r')
            ->getQuery()
            ->getResult();
    }

    public function findWithSearchQuery(?string $search, ?string $userId): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.author', 'a')
            ->addSelect('a')
            ->leftJoin('c.article', 'r')
            ->addSelect('r');

        if ($search && $search != '') {
            $qb
                ->andWhere('c.text LIKE :search OR r.title LIKE :search')
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

    public function search(string $search)
    {
        if( empty($search)) return [];
        $search = '%' . trim($search) . '%';
        return $this->createQueryBuilder('com')
            ->leftJoin('com.author', 'au')
            ->addSelect('au')
            ->andWhere('
                com.text LIKE :search
                OR au.firstName LIKE :search
                OR au.surname LIKE :search
                OR au.patronymic LIKE :search
           ')
            ->andWhere('com.publishedAt IS NOT NULL')
            ->andWhere('au.banned IS NULL')
            ->andWhere('au.deletedAt IS NULL')
            ->setParameter('search', $search)
            ->getQuery()
            ->getResult();
    }

}
