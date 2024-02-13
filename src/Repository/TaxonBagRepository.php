<?php

namespace App\Repository;

use App\Entity\TaxonBag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * @extends ServiceEntityRepository<TaxonBag>
 *
 * @method TaxonBag|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaxonBag|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaxonBag[]    findAll()
 * @method TaxonBag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxonBagRepository extends ServiceEntityRepository implements UniquenessRepositoryInterface
{
    private const SEARCH_FIELDS = [
        'name',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaxonBag::class);
    }

    public function getTaxonBagsQuery(array $search): Query
    {
        $query =  $this->createQueryBuilder('tb');

        if (array_key_exists('fields', $search)) {
            foreach ($search['fields'] as $field => $value) {
                if (!in_array($field, self::SEARCH_FIELDS)) {
                    throw new BadRequestException('Invalid search parameter');
                }

                $query->andWhere('tb.'.$field.' LIKE :'.$field)
                    ->setParameter($field, '%'.$value.'%');
            }
        }

        return $query->orderBy('tb.id', 'DESC')
            ->getQuery();
    }

    public function findOneByField(string $value, ?int $excludedId = null): ?TaxonBag
    {
        $query = $this->createQueryBuilder('tb')
            ->andWhere('tb.name = :name')
            ->setParameter('name', $value);

        if ($excludedId !== null) {
            $query->andWhere('tb.id != :id')
                ->setParameter('id', $excludedId);
        }

        return $query->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return TaxonBag[] Returns an array of TaxonBag objects
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
