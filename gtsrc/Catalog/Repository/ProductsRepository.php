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

class ProductsRepository extends EntityRepository
{
    public function getAllSkus() {
        $conn = $this->_em->getConnection();

        $sql = /** @lang MySQL */ "SELECT sku FROM products";

        /** @var string[] $skus */
        $skus = $conn->executeQuery($sql)->fetchAll(FetchMode::COLUMN);

        return $skus;
    }
}