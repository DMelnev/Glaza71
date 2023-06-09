<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->add($user, true);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getUserByCode(string $code): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.confirmed IS NULL OR u.confirmed <= :time')
            ->andWhere('u.activationCode = :code')
            ->setParameter('time', (new \DateTime('-2 min')))
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllSortedByName()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.banned IS NULL')
            ->orderBy('a.firstName')
            ->getQuery()
            ->getResult();
    }
    public function findAllNotBanned()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.banned IS NULL')
            ->orderBy('a.firstName')
;
    }
    public function findBanned()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.banned IS NOT NULL')
            ->orderBy('a.firstName')
            ;
    }

}
