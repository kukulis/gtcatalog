<?php

namespace Gt\Catalog\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}
