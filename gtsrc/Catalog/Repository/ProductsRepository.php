<?php
/**
 * ProductsRepository.php
 * Created by Giedrius Tumelis.
 * Date: 2021-02-15
 * Time: 11:05
 */

namespace Gt\Catalog\Repository;


use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityRepository;
use Gt\Catalog\Entity\Product;

class ProductsRepository extends EntityRepository
{
    /**
     * @return string[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllSkus() {
        $conn = $this->_em->getConnection();

        $sql = /** @lang MySQL */ "SELECT sku FROM products";

        /** @var string[] $skus */
        $skus = $conn->executeQuery($sql)->fetchAll(FetchMode::COLUMN);

        return $skus;
    }

    /**
     * @param string $brandName
     * @return int
     */
    public function getProductsCountByBrand($brandName) {
        $class = Product::class;
        $dql = /** @lang DQL */ "SELECT count(p.sku)  FROM $class p WHERE p.brand=:brandName";
        $query = $this->_em->createQuery($dql);
        $query->setParameter('brandName', $brandName );

        /** @var int[][] $countArr */
        $countArr = $query->getResult();
        $first = reset($countArr);
        $count = reset($first);
        return $count;
    }

    /**
     * @param string $oldBrand
     * @param string $newBrand
     * @return int
     */
    public function updateBrands($oldBrand, $newBrand ) {
        $class = Product::class;
        $dql = /** @lang DQL */ "UPDATE $class p SET p.brand=:newBrand WHERE p.brand=:oldBrand";

        $query = $this->_em->createQuery($dql);
        $query->setParameter('newBrand', $newBrand );
        $query->setParameter('oldBrand', $oldBrand );
        $rez = $query->execute();
        return $rez;
    }
}