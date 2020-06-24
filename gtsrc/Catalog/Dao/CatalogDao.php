<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 13.32
 */

namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Entity\Product;
use Psr\Log\LoggerInterface;

class CatalogDao
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var Registry */
    private $doctrine;

    /**
     * CatalogDao constructor.
     * @param LoggerInterface $logger
     * @param Registry $doctrine
     */
    public function __construct(LoggerInterface $logger, Registry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }


    /**
     * @param int $offset
     * @param int $limit
     * @return Product[]
     */
    public function getProductsList(  $offset, $limit ) {
        $productClass = Product::class;
        $dql = /** @lang DQL */ "SELECT p from $productClass p";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        /** @var Product[] $products */
        $products = $em->createQuery($dql)->setMaxResults($limit)->setFirstResult($offset)->execute();

        return $products;
    }

    /**
     * @param string $sku
     * @return Product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getProduct( $sku ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        /** @var Product $product */
        $product = $em->find(Product::class, $sku );

        return $product;
    }
}