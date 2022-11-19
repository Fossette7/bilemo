<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
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
   * @param Customer $customer
   * @param int $page
   * @param int $limit
   * @return float|int|mixed|string
   */
  public function findAllPagination(Customer $customer, int $page = 1, int $limit = 3)
  {
    // Utilisation de l'objet Customer (obligatoir) pour obtenir tout les utilisateur ayant en relation le customer $customer
    $querybuilder = $this->createQueryBuilder('builder')
      ->select('u')
      ->from('App\Entity\User', 'u')
      // On joint l'entité user avec le customer
      ->innerJoin('u.customer', 'c')
      // On indique que la valeur u.customer doit-être égal à :customerObject
      ->where('u.customer = :customerObject')
      ->setFirstResult(($page - 1) * $limit)
      ->setMaxResults($limit)
      // On associe la valeur $customer àà notre "clée" :customerObject (pour évité les injection sql)
      ->setParameter(':customerObject', $customer);

    return $querybuilder->getQuery()->getResult();
  }

//    /**
//     * @return User[] Returns an array of User objects
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

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
