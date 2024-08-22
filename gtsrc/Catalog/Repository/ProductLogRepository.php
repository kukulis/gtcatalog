<?php

namespace Gt\Catalog\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Gt\Catalog\Data\ProductLogFilter;
use Gt\Catalog\Entity\ProductLog;
use Doctrine\Persistence\ManagerRegistry;

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

    public function save(ProductLog $product)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($product);
        $entityManager->flush();
    }

    public function getList(ProductLogFilter $filter) {
        $builder = $this->createQueryBuilder('pl');
        $builder->setMaxResults($filter->getLimit());

        if ( !empty($filter->getOffset())) {
            $builder->setFirstResult($filter->getOffset());
        }

        if ( !empty($filter->getSku())) {
            $builder->andWhere( "pl.sku like :sku");
            $builder->setParameter("sku", '%'.$filter->getSku().'%' );
        }

        if ( !empty($filter->getLanguage())) {
            $builder->andWhere( "pl.language like :language");
            $builder->setParameter("language", '%'.$filter->getLanguage().'%' );
        }

        $builder->orderBy('pl.id', 'DESC' );

        /** @var ProductLog[] $productLogs */
        $productLogs = $builder->getQuery()->getResult();
        return $productLogs;
    }
}
