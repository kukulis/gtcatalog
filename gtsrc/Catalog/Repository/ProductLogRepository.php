<?php

namespace Gt\Catalog\Repository;

use Gt\Catalog\Data\ProductLogFilter;
use Gt\Catalog\Entity\ProductLog;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;


/**
 *
 * @method ProductLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductLog[]    findAll()
 * @method ProductLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductLogRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductLog::class);
    }

    public function add(ProductLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProductLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ProductLog[] Returns an array of ProductLog objects
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

//    public function findOneBySomeField($value): ?ProductLog
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getList(ProductLogFilter $filter): array
    {
        $builder = $this->createQueryBuilder('b');
        $builder->setMaxResults($filter->getLimit());

        if ( !empty($filter->getOffset())) {
            $builder->setFirstResult($filter->getOffset());
        }

//        if ( !empty($filter->getLikeName())) {
//            $builder->andWhere( "b.brand like :likeBrand");
//            $builder->setParameter("likeBrand", '%'.$filter->getLikeName().'%' );
//        }
//
//        if ( !empty($filter->getStartsLike())) {
//            $builder->andWhere( "b.brand like :startsLike");
//            $builder->setParameter("startsLike", $filter->getStartsLike().'%' );
//        }

//        $builder->orderBy('b.brand' );

        /** @var ProductLog[] $productLog */
        $productLog = $builder->getQuery()->getResult();

        return $productLog;
    }
}
