<?php

namespace App\Repository;

use App\Entity\Taxon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * @extends ServiceEntityRepository<Taxon>
 *
 * @method Taxon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taxon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taxon[]    findAll()
 * @method Taxon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxonRepository extends ServiceEntityRepository
{
    private const SEARCH_FIELDS = [
        'title',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taxon::class);
    }

    public function getTaxaQuery(string $taxonomyName, array $search): Query
    {
        $query = $this->createQueryBuilder('t')
            ->join('t.taxonomy', 'taxonomy')
            ->join('t.createdBy', 'creator')
            ->join('t.updatedBy', 'modifier')
            ->andWhere('taxonomy.name = :taxonomy')
            ->setParameter('taxonomy', $taxonomyName)
            ->select('t', 'taxonomy', 'creator', 'modifier');

        if (array_key_exists('fields', $search)) {
            foreach ($search['fields'] as $field => $value) {
                if (!in_array($field, self::SEARCH_FIELDS)) {
                    throw new BadRequestException('Invalid search parameter');
                }

                $query->andWhere('t.'.$field.' LIKE :'.$field)
                    ->setParameter($field, '%'.$value.'%');
            }
        }

        return $query->orderBy('t.id', 'DESC')
            ->getQuery();
    }

    public function findOneByTaxonomyNameAndId(string $taxonomyName, int $id): ?Taxon
    {
        return $this->createQueryBuilder('t')
            ->join('t.taxonomy', 'taxonomy')
            ->join('t.createdBy', 'creator')
            ->join('t.updatedBy', 'modifier')
            ->andWhere('taxonomy.name = :taxonomy')
            ->andWhere('t.id = :id')
            ->setParameter('taxonomy', $taxonomyName)
            ->setParameter('id', $id)
            ->select('t', 'taxonomy', 'creator', 'modifier')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
