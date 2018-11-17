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

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function getAllPosts()
    {
        return $this->findAll();
    }

    public function getPost($id)
    {
        /**
         * Ove moze da stoji bilo stane mora post, moze npr. p ili po ili sta god,
         * Bitno je samo da posle preko toga pristupas cemu treba u upitu, ovde npr. id-ju
         */
        $qb = $this->createQueryBuilder('post')
            /** Vidi ovaj @link https://symfony.com/doc/current/doctrine.html#querying-for-objects-the-repository, primer sa price */
            ->where('post.id= :parameter_id')
            ->setParameter('parameter_id', $id);

        return $qb->getQuery()->execute();

    }
}
