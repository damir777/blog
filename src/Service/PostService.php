<?php

namespace App\Service;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostService
{
    public function __construct(private readonly PostRepository $repository, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getPosts(array $search): Paginator
    {
        $query = $this->repository->getPostsQuery($search);

        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($search['offset'])
            ->setMaxResults($search['limit']);

        return $paginator;
    }

    public function insertPost(array $properties): Post
    {
        $post = new Post();

        foreach ($properties as $property => $value) {
            $setMethod = 'set'.ucfirst($property);

            $post->$setMethod($value);
        }

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return $post;
    }

    public function getPost(int $id): Post
    {
        $post = $this->repository->find($id);

        if ($post === null) {
            throw new NotFoundHttpException('Post with ID "'.$id.'" not found');
        }

        return $post;
    }

    public function updatePost(array $properties, int $id): Post
    {
        $post = $this->getPost($id);

        foreach ($properties as $property => $value) {
            $setMethod = 'set'.ucfirst($property);

            $post->$setMethod($value);
        }

        $this->entityManager->flush();

        return $post;
    }

    public function deletePost(int $id): void
    {
        $post = $this->getPost($id);

        $this->entityManager->remove($post);
        $this->entityManager->flush();
    }
}