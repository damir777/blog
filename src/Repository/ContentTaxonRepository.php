<?php

namespace App\Repository;

use App\Entity\ContentTaxon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContentTaxon>
 *
 * @method ContentTaxon|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContentTaxon|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContentTaxon[]    findAll()
 * @method ContentTaxon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentTaxonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContentTaxon::class);
    }

    public function getContentTaxaQuery(int $postId, string $bagName): Query
    {
        return $this->createQueryBuilder('ct')
            ->join('ct.taxonBag', 'bag')
            ->join('ct.taxon', 'taxon')
            ->join('ct.createdBy', 'creator')
            ->join('ct.updatedBy', 'modifier')
            ->andWhere('ct.post = :post')
            ->andWhere('bag.name = :bag')
            ->setParameter('post', $postId)
            ->setParameter('bag', $bagName)
            ->select('ct', 'taxon', 'creator', 'modifier')
            ->orderBy('ct.id', 'DESC')
            ->getQuery();
    }

    public function findOneById(int $postId, string $bagName, int $id): ?ContentTaxon
    {
        return $this->createQueryBuilder('ct')
            ->join('ct.taxonBag', 'bag')
            ->join('ct.taxon', 'taxon')
            ->join('ct.createdBy', 'creator')
            ->join('ct.updatedBy', 'modifier')
            ->andWhere('ct.post = :post')
            ->andWhere('bag.name = :bag')
            ->andWhere('ct.id = :id')
            ->setParameter('post', $postId)
            ->setParameter('bag', $bagName)
            ->setParameter('id', $id)
            ->select('ct', 'taxon', 'creator', 'modifier')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByTaxon(int $postId, string $bagName, int $taxonId): ?ContentTaxon
    {
        return $this->createQueryBuilder('ct')
            ->join('ct.taxonBag', 'bag')
            ->andWhere('ct.post = :post')
            ->andWhere('bag.name = :bag')
            ->andWhere('ct.taxon = :taxon')
            ->setParameter('post', $postId)
            ->setParameter('bag', $bagName)
            ->setParameter('taxon', $taxonId)
            ->select('ct')
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return ContentTaxon[] Returns an array of ContentTaxon objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ContentTaxon
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
