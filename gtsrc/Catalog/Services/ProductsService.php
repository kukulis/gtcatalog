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
use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Exception\CatalogDetailedException;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\RelatedObject;
use Gt\Catalog\Exception\RelatedObjectClassificator;
use Psr\Log\LoggerInterface;

class ProductsService
{
    const PAGE_SIZE=10;

    /** @var LoggerInterface */
    private $logger;

    /** @var CatalogDao */
    private $catalogDao;


    /**
     * @var LanguageDao
     */
    private $languageDao;

    /**
     * ProductsService constructor.
     * @param LoggerInterface $logger
     * @param CatalogDao $catalogDao
     */
    public function __construct(LoggerInterface $logger, CatalogDao $catalogDao, LanguageDao $languageDao )
    {
        $this->logger = $logger;
        $this->catalogDao = $catalogDao;
        $this->languageDao = $languageDao;
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

    /**
     * @param RelatedObject[] $objects
     * @return
     */
    public function getSuggestions($objects) {
        foreach ( $objects as $o ) {

            if ( get_class($o) == RelatedObjectClassificator::class ) {
                /** @var RelatedObjectClassificator $roc */
                $roc = $o;
                $simmilarClassificators = $this->catalogDao->loadSimmilarClassificators($roc->classificatorCode, $roc->correctCode, 5 );

                $codes = array_map ( [Classificator::class,  'lambdaGetCode' ], $simmilarClassificators );
                $roc->suggestions = $codes;
            }
        }
        return $objects;
    }

    /**
     * @param $sku
     * @param $languageCode
     * @return \Gt\Catalog\Entity\ProductLanguage
     * @throws CatalogErrorException
     */
    public function getProductLanguage ( $sku, $languageCode) {
        try {
            $pl = $this->catalogDao->getProductLanguage($sku, $languageCode);

            if (is_object($pl)) {
                return $pl;
            }

            // kuriam naują
            $language = $this->languageDao->getLanguage($languageCode);
            if (empty ($language)) {
                throw new CatalogErrorException('There is no language with code [' . $languageCode.']' );
            }

            $product = $this->catalogDao->getProduct($sku );
            if ( empty($product)) {
                throw new CatalogErrorException('There is no product with sku=['.$sku.']' );
            }

            $productLanguage = new ProductLanguage();
            $productLanguage->setProduct($product);
            $productLanguage->setLanguage($language);

            return $productLanguage;
        } catch (ORMException $e ) {
            throw new CatalogErrorException($e->getMessage());
        }
    }

    /**
     * @param ProductLanguage $pl
     * @throws CatalogErrorException
     */
    public function storeProductLanguage ( ProductLanguage $pl ) {
        $this->catalogDao->storeProductLanguage($pl);
        $this->catalogDao->flush();
    }


}