<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Adds a Post entity to the database.
     *
     * @param Post $post The Post entity to add.
     * @param bool $flush Whether to flush the changes to the database immediately.
     */
    public function add(Post $post, bool $flush = false): void
    {
        $this->_em->persist($post);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Removes a Post entity from the database.
     *
     * @param Post $post The Post entity to remove.
     * @param bool $flush Whether to flush the changes to the database immediately.
     */
    public function remove(Post $post, bool $flush = false): void
    {
        $this->_em->remove($post);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Finds a Post entity by its ID and returns selected fields.
     *
     * @param int $id The ID of the Post entity to find.
     * @return array|null An array with the selected fields or null if no Post entity is found.
     */
    public function findPost($id): ?array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id, p.title, p.type')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}