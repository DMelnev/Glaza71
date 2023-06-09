<?php

namespace App\Repository;

use App\Entity\MainPage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MainPage>
 *
 * @method MainPage|null find($id, $lockMode = null, $lockVersion = null)
 * @method MainPage|null findOneBy(array $criteria, array $orderBy = null)
 * @method MainPage[]    findAll()
 * @method MainPage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MainPageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MainPage::class);
    }

    public function add(MainPage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MainPage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search(string $search)
    {
        if( empty($search)) return [];
        $search = '%' . trim($search) . '%';
        return $this->createQueryBuilder('main')
            ->andWhere('
                main.headTitle LIKE :search
                OR main.keywords LIKE :search
                OR main.text LIKE :search
                OR main.title LIKE :search
           ')
            ->setParameter('search', $search)
            ->getQuery()
            ->getResult();
    }
}