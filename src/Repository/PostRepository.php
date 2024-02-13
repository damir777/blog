<?php

namespace App\Repository;

use App\Entity\ContentTaxon;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository implements UniquenessRepositoryInterface
{
    private const SEARCH_FIELDS = [
        'title',
        'body',
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function getPostsQuery(array $search): Query
    {
        $query = $this->createQueryBuilder('p')
            ->join('p.createdBy', 'creator')
            ->join('p.updatedBy', 'modifier')
            ->select('p', 'creator', 'modifier');

        if (array_key_exists('fields', $search)) {
            foreach ($search['fields'] as $field => $value) {
                if (!in_array($field, self::SEARCH_FIELDS)) {
                    throw new BadRequestException('Invalid search parameter');
                }

                $query->andWhere('p.'.$field.' LIKE :'.$field)
                    ->setParameter($field, '%'.$value.'%');
            }
        }

        if (array_key_exists('content', $search)) {
            $query->join(ContentTaxon::class, 'contentTaxon', Join::WITH, 'p.id = contentTaxon.post');

            foreach ($search['content'] as $bagId => $postId) {
                $query->andWhere('contentTaxon.post = :post')
                    ->andWhere('contentTaxon.taxonBag = :bag')
                    ->setParameter('post', $postId)
                    ->setParameter('bag', $bagId);
            }
        }

        return $query->orderBy('p.id', 'DESC')
            ->getQuery();
    }

    public function findOneByField(string $value, ?int $excludedId = null): ?Post
    {
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.slug = :slug')
            ->setParameter('slug', $value);

        if ($excludedId !== null) {
            $query->andWhere('p.id != :id')
                ->setParameter('id', $excludedId);
        }

        return $query->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Post[] Returns an array of Post objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
}
