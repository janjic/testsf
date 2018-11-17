<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;


class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAllUsers()
    {
        return $this->findAll();
    }

    public function insertUser(User $user)
    {
        try {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        } catch (ORMException $e) {
            return false;
        }
        return $user;
    }

    public function insertUsers($users)
    {
        try {
            foreach ($users as $user) {
                $this->getEntityManager()->persist($user);
            }
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            return false;
        }
        return true;

    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUser($id)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.id= :user_id')
            ->setParameter('user_id', $id);

        //Kada nam treba jedan rezultat zovemo getSingleResult
        return $qb->getQuery()->getSingleResult();

    }
}
