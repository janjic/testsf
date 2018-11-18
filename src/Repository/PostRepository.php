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
    private $userRepository;
    private $tagRepository;

    public function __construct(ManagerRegistry $registry, UserRepository $userRepository, TagRepository $tagRepository)
    {
        parent::__construct($registry, Post::class);
        $this->userRepository = $userRepository;
        $this->tagRepository = $tagRepository;
    }

    public function getAllPosts($limit=null, $perPage=null, $page=null)
    {
        if(!$page) {
            $page = 1;
        }
        $offset = null;
        if ($page && $perPage) {
            $offset = ($page - 1) * $perPage;
            $limit = $perPage;
        }
        return
            array(
                'data'=>$this->findBy([],null, $limit, $offset),
                'meta'=> array(
                    'count' => $count = $this->count([]),
                    'page' => $page
                )
            );
    }

    /**
     * Ova metoda je bitna jako zbog veza i cuvanja veza
     * @param Post $post
     * @return Post|array
     */
    public function savePost(Post $post)
    {
        try {
            //Ovaj author je nastao procesom deserializacije, ali on nema doctrine podatke,tj. nije iz baze
            $author = $post->getAuthor();
            //Da bi se sacuvao entitet on mora biti iz baze
            $authorFromDb = $this->userRepository->getUser($author->getId());
            $post->setAuthor($authorFromDb);

            foreach ($post->getTags() as $tag) {
                $dbTag = $this->tagRepository->getTagByName($tag->getName());
                if ($dbTag) {
                    //Ako vec taj postoji u bazi, izbaci trenutni i ubaci iz baze
                    $post->removeTag($tag);
                    $post->addTag($dbTag);
                }
            }
            //JAKO BITNO:  TAGOVI SE CUVAJU SAMO POSTO IMAJU cascade={"persist"}
            //DA NEMAJU MORALI BI DA INJECTUJEMO I NJIHOV RIPOSITORY I DA CUVAMO tag po tag
            $this->getEntityManager()->persist($post);
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            return array(
                'status' => 'Error',
                'message' => $e->getMessage()
            );
        }

        return $post;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
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

        return $qb->getQuery()->getSingleResult();

    }
}
