<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 23.07
 */

namespace Gt\Catalog\Services;


use Doctrine\ORM\ORMException;
use Gt\Catalog\Dao\CatalogDao;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Exception\CatalogDetailedException;
use Gt\Catalog\Exception\CatalogErrorException;
use Psr\Log\LoggerInterface;

class ProductsService
{
    const PAGE_SIZE=10;

    /** @var LoggerInterface */
    private $logger;

    /** @var CatalogDao */
    private $catalogDao;

    /**
     * ProductsService constructor.
     * @param LoggerInterface $logger
     * @param CatalogDao $catalogDao
     */
    public function __construct(LoggerInterface $logger, CatalogDao $catalogDao)
    {
        $this->logger = $logger;
        $this->catalogDao = $catalogDao;
    }

    /**
     * @param int $page
     * @return Product[]
     */
    public function getProducts ( $page=0) {
        $products = $this->catalogDao->getProductsList($page*self::PAGE_SIZE, self::PAGE_SIZE );
        return $products;
    }

    /**
     * @param string $sku
     * @return Product
     * @throws CatalogErrorException
     */
    public function getProduct( $sku ) {
        // čia validaciją dar padarysim sku ir pan.
        try {
            $product = $this->catalogDao->getProduct($sku);
        } catch ( ORMException $e ) {
            throw new CatalogErrorException($e->getMessage());
        }
        return $product;
    }

    /**
     * @param Product $product
     * @throws CatalogErrorException
     * @throws CatalogDetailedException
     */
    public function storeProduct(Product $product) {
        $this->catalogDao->storeProduct($product);
        $this->catalogDao->assignAssociations($product);
        $this->catalogDao->flush();
    }


}