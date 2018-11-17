<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * This custom Doctrine repository is empty because so far we don't need any custom
 * method to query for application user information. But it's always a good practice
 * to define a custom repository that will be used when the application grows.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTag($id)
    {
        /**
         * Ove moze da stoji bilo stane mora post, moze npr. p ili po ili sta god,
         * Bitno je samo da posle preko toga pristupas cemu treba u upitu, ovde npr. id-ju
         */
        $qb = $this->createQueryBuilder('tag')
            ->where('tag.id= :parameter_id')
            ->setParameter('parameter_id', $id);

        return $qb->getQuery()->getOneOrNullResult();

    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTagByName($name)
    {
        $qb = $this->createQueryBuilder('tag')
            ->where('tag.name = :parameter_name')
            ->setParameter('parameter_name', $name);

        return $qb->getQuery()->getOneOrNullResult();

    }
}
