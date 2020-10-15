<?php
/**
 * ProductsRestService.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-15
 * Time: 15:07
 */

namespace Gt\Catalog\Services\Rest;


use Gt\Catalog\Dao\CatalogDao;
use Gt\Catalog\Dao\CategoryDao;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Rest\Legacy\KatalogasPreke;
use Psr\Log\LoggerInterface;

class ProductsRestService
{
    /** @var LoggerInterface */
    private $logger;

    /** @var CatalogDao */
    private $catalogDao;

    /** @var CategoryDao */
    private $categoryDao;

    /** @var array  */
    private $languagesMap=[];

    /**
     * ProductsRestService constructor.
     * @param LoggerInterface $logger
     * @param CatalogDao $catalogDao
     * @param CategoryDao $categoryDao
     */
    public function __construct(LoggerInterface $logger, CatalogDao $catalogDao, CategoryDao $categoryDao)
    {
        $this->logger = $logger;
        $this->catalogDao = $catalogDao;
        $this->categoryDao = $categoryDao;

        // TODO picure dao
    }


    /**
     * @param string[] $skus
     * @param string $language
     * @return KatalogasPreke[]
     */
    public function getLegacyPrekes ($skus, $language ) {

        if ( !array_key_exists($language, $this->languagesMap )) {
            throw new CatalogValidateException('Unavailable language '.$language );
        }

        $langCode = $this->languagesMap[$language];


        // 1) load all data from database
        $products = $this->catalogDao->getProductsLangsWithSubobjects($skus, $langCode);

//        $this->categoryDao->getProductCategories()
        // TODO pictures
        // TODO categories
        // TODO map pictures and categories to products

        // 2) build legacy structure for returning
        /** @var KatalogasPreke[] $prekes */
        $prekes = [];


        foreach ( $products as $product ) {
            $preke = ProductToKatalogasPrekeMapper::mapProduct2KatalogasPreke($product, [], []);
            $prekes[] = $preke;
        }

        return $prekes;
    }

    /**
     * @param array $languagesMap
     */
    public function setLanguagesMap(array $languagesMap): void
    {
        $this->languagesMap = $languagesMap;
    }
}