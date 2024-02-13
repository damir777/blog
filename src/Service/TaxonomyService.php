<?php

namespace App\Service;

use App\Entity\Taxonomy;
use App\Repository\TaxonomyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaxonomyService
{
    public function __construct(private readonly TaxonomyRepository $repository, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function getTaxonomies(array $search): Paginator
    {
        $query = $this->repository->getTaxonomiesQuery($search);

        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($search['offset'])
            ->setMaxResults($search['limit']);

        return $paginator;
    }

    public function insertTaxonomy(string $name): Taxonomy
    {
        $taxonomy = new Taxonomy();
        $taxonomy->setName($name);

        $this->entityManager->persist($taxonomy);
        $this->entityManager->flush();

        return $taxonomy;
    }

    public function getTaxonomy(int $id): Taxonomy
    {
        $taxonomy = $this->repository->find($id);

        if ($taxonomy === null) {
            throw new NotFoundHttpException('Taxonomy with ID "'.$id.'" not found');
        }

        return $taxonomy;
    }

    public function updateTaxonomy(string $name, int $id): Taxonomy
    {
        $taxonomy = $this->getTaxonomy($id);
        $taxonomy->setName($name);

        $this->entityManager->flush();

        return $taxonomy;
    }

    public function deleteTaxonomy(int $id): void
    {
        $taxonomy = $this->getTaxonomy($id);

        $this->entityManager->remove($taxonomy);
        $this->entityManager->flush();
    }

    public function findById(int $id): ?Taxonomy
    {
        return $this->repository->find($id);
    }

    public function findByName(string $name): Taxonomy
    {
        $taxonomy = $this->repository->findOneBy(['name' => $name]);

        if ($taxonomy === null) {
            throw new NotFoundHttpException('Taxonomy with name "'.$name.'" not found');
        }

        return $taxonomy;
    }
}
