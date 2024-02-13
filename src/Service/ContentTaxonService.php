<?php

namespace App\Service;

use App\Entity\ContentTaxon;
use App\Repository\ContentTaxonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentTaxonService
{
    public function __construct(
        private readonly ContentTaxonRepository $repository,
        private readonly PostService $postService,
        private readonly TaxonBagService $taxonBagService,
        private readonly TaxonService $taxonService,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getContentTaxa(int $postId, string $bagName, array $search): Paginator
    {
        $this->postService->getPost($postId);
        $this->taxonBagService->findByName($bagName);

        $query = $this->repository->getContentTaxaQuery($postId, $bagName);

        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($search['offset'])
            ->setMaxResults($search['limit']);

        return $paginator;
    }

    public function assignContentTaxon(int $postId, string $bagName, int $taxonId): ContentTaxon
    {
        $post = $this->postService->getPost($postId);
        $taxonBag = $this->taxonBagService->findByName($bagName);
        $taxon = $this->taxonService->findById($taxonId, true);

        $assignedTaxon = $this->repository->findOneByTaxon($postId, $bagName, $taxonId);

        if ($assignedTaxon instanceof ContentTaxon) {
            throw new BadRequestException('A taxon is already applied');
        }

        $contentTaxon = new ContentTaxon();
        $contentTaxon->setTaxonBag($taxonBag);
        $contentTaxon->setTaxon($taxon);
        $contentTaxon->setPost($post);
        $this->entityManager->persist($contentTaxon);

        $this->entityManager->flush();

        return $contentTaxon;
    }

    public function getContentTaxon(int $postId, string $bagName, int $id): ContentTaxon
    {
        $this->postService->getPost($postId);
        $this->taxonBagService->findByName($bagName);
        $contentTaxon = $this->repository->findOneById($postId, $bagName, $id);

        if ($contentTaxon === null) {
            throw new NotFoundHttpException('Content taxon with ID "'.$id.'" not found');
        }

        return $contentTaxon;
    }

    public function deleteContentTaxon(int $postId, string $bagName, int $id): void
    {
        $contentTaxon = $this->getContentTaxon($postId, $bagName, $id);

        $this->entityManager->remove($contentTaxon);
        $this->entityManager->flush();
    }
}
