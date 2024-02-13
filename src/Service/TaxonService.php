<?php

namespace App\Service;

use App\Entity\Taxon;
use App\Repository\TaxonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaxonService
{
    public function __construct(
        private readonly TaxonRepository $repository,
        private readonly TaxonomyService $taxonomyService,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getTaxa(string $taxonomyName, array $search): Paginator
    {
        $this->taxonomyService->findByName($taxonomyName);

        $query = $this->repository->getTaxaQuery($taxonomyName, $search);

        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($search['offset'])
            ->setMaxResults($search['limit']);

        return $paginator;
    }

    public function insertTaxon(array $properties): Taxon
    {
        $taxonomy = $this->taxonomyService->findByName($properties['taxonomy']);

        $taxon = new Taxon();
        $taxon->setTitle($properties['title']);
        $taxon->setTaxonomy($taxonomy);

        $this->entityManager->persist($taxon);
        $this->entityManager->flush();

        return $taxon;
    }

    public function getTaxon(string $taxonomyName, int $id): Taxon
    {
        $this->taxonomyService->findByName($taxonomyName);

        $taxon = $this->repository->findOneByTaxonomyNameAndId($taxonomyName, $id);

        if ($taxon === null) {
            throw new NotFoundHttpException('Taxon with ID "'.$id.'" not found');
        }

        return $taxon;
    }

    public function updateTaxon(array $properties, int $id): Taxon
    {
        $taxon = $this->getTaxon($properties['taxonomy'], $id);
        $taxon->setTitle($properties['title']);

        $this->entityManager->flush();

        return $taxon;
    }

    public function deleteTaxon(string $taxonomyName, int $id): void
    {
        $taxon = $this->getTaxon($taxonomyName, $id);

        $this->entityManager->remove($taxon);
        $this->entityManager->flush();
    }

    public function findById(int $id, bool $check = false): ?Taxon
    {
        $taxon = $this->repository->find($id);

        if ($check === false) {
            return $taxon;
        }

        if ($taxon === null) {
            throw new NotFoundHttpException('Taxon with ID "'.$id.'" not found');
        }

        return $taxon;
    }
}
