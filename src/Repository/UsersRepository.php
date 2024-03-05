<?php

namespace App\Repository;

use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<Users>
* @implements PasswordUpgraderInterface<Users>
 *
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Users) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
    
    public function findByFiltersAndSort($search,$critere,$crit,$order)
    {
        return $this->createQueryBuilder('a')
        ->where("a.$critere LIKE :bonjour")
        ->setParameter('bonjour',"$search%")
        ->orderBy("a.$crit",$order)
        ->getQuery()
        ->getResult();
    }
    public function birthDay()
    {
        $dql = "SELECT e FROM App\Entity\Users e 
                WHERE SUBSTRING(e.dateofbirth, 6, 2) = SUBSTRING(CURRENT_DATE(), 6, 2) 
                AND SUBSTRING(e.dateofbirth, 9, 2) = SUBSTRING(CURRENT_DATE(), 9, 2)";
        return $this->getEntityManager()
            ->createQuery($dql)
            ->getResult();
    }
    public function Blocked()
    {
        $dql = "SELECT e FROM App\Entity\Users e 
                WHERE e.blockunitl IS NOT NULL AND  DATE_DIFF(e.blockunitl, CURRENT_DATE())>0";
        return $this->getEntityManager()
            ->createQuery($dql)
           
            ->getResult();
    }


//    /**
//     * @return Users[] Returns an array of Users objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Users
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
