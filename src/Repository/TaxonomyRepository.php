<?php

namespace App\Repository;

use App\Entity\Taxonomy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * @extends ServiceEntityRepository<Taxonomy>
 *
 * @method Taxonomy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taxonomy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taxonomy[]    findAll()
 * @method Taxonomy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxonomyRepository extends ServiceEntityRepository implements UniquenessRepositoryInterface
{
    private const SEARCH_FIELDS = [
        'name',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Taxonomy::class);
    }

    public function getTaxonomiesQuery(array $search): Query
    {
        $query = $this->createQueryBuilder('t');

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

    public function findOneByField(string $value, ?int $excludedId = null): ?Taxonomy
    {
        $query = $this->createQueryBuilder('t')
            ->andWhere('t.name = :name')
            ->setParameter('name', $value);

        if ($excludedId !== null) {
            $query->andWhere('t.id != :id')
                ->setParameter('id', $excludedId);
        }

        return $query->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Taxonomy[] Returns an array of Taxonomy objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
}
