<?php

namespace App\Service;

use App\Entity\TaxonBag;
use App\Repository\TaxonBagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaxonBagService
{
    public function __construct(
        private readonly TaxonBagRepository $repository,
        private readonly TaxonomyService $taxonomyService,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getTaxonBags(array $search): Paginator
    {
        $query = $this->repository->getTaxonBagsQuery($search);

        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($search['offset'])
            ->setMaxResults($search['limit']);

        return $paginator;
    }

    public function insertTaxonBag(array $properties): TaxonBag
    {
        $taxonomy = $this->taxonomyService->getTaxonomy($properties['taxonomy']);

        $taxonBag = new TaxonBag();
        $taxonBag->setName($properties['name']);
        $taxonBag->setTaxonomy($taxonomy);

        $this->entityManager->persist($taxonBag);
        $this->entityManager->flush();

        return $taxonBag;
    }

    public function getTaxonBag(int $id): TaxonBag
    {
        $bag = $this->repository->find($id);

        if ($bag === null) {
            throw new NotFoundHttpException('Taxon bag with ID "'.$id.'" not found');
        }

        return $bag;
    }

    public function updateTaxonBag(array $properties, int $id): TaxonBag
    {
        $taxonBag = $this->getTaxonBag($id);
        $taxonBag->setName($properties['name']);

        $this->entityManager->flush();

        return $taxonBag;
    }

    public function deleteTaxonBag(int $id): void
    {
        $taxonBag = $this->getTaxonBag($id);

        $this->entityManager->remove($taxonBag);
        $this->entityManager->flush();
    }

    public function findByName(string $name): TaxonBag
    {
        $bag = $this->repository->findOneBy(['name' => $name]);

        if ($bag === null) {
            throw new NotFoundHttpException('Taxon bag with name "'.$name.'" not found');
        }

        return $bag;
    }
}
