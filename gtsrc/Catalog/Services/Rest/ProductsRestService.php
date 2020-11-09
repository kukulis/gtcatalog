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
use Gt\Catalog\Dao\PicturesDao;
use Gt\Catalog\Entity\CategoryLanguage;
use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\ClassificatorLanguage;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductCategory;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Entity\ProductPicture;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Rest\Legacy\KatalogasPreke;
use Gt\Catalog\Services\PicturesService;
use Gt\Catalog\Utils\ProductsHelper;
use Psr\Log\LoggerInterface;

class ProductsRestService
{
    const MAX_PORTION = 500;
    const STEP = 100;

    /** @var LoggerInterface */
    private $logger;

    /** @var CatalogDao */
    private $catalogDao;

    /** @var CategoryDao */
    private $categoryDao;

    /** @var PicturesDao */
    private $picturesDao;

    /** @var PicturesService */
    private $picturesSevice;

    /** @var array  */
    private $languagesMap=[];

    /**
     * ProductsRestService constructor.
     * @param LoggerInterface $logger
     * @param CatalogDao $catalogDao
     * @param CategoryDao $categoryDao
     */
    public function __construct(LoggerInterface $logger,
                                CatalogDao $catalogDao,
                                CategoryDao $categoryDao,
                                PicturesDao $picturesDao,
                                PicturesService $picturesService
                                )
    {
        $this->logger = $logger;
        $this->catalogDao = $catalogDao;
        $this->categoryDao = $categoryDao;
        $this->picturesDao = $picturesDao;
        $this->picturesSevice = $picturesService;
    }


    /**
     * @param string[] $skus
     * @param string $language
     * @return KatalogasPreke[]
     * @throws CatalogValidateException
     */
    public function getLegacyPrekes ($skus, $language ) {
        if ( count($skus) > self::MAX_PORTION ) {
            throw new CatalogValidateException('Maximum skus in request is limited to '.self::MAX_PORTION );
        }

        if ( !array_key_exists($language, $this->languagesMap )) {
            throw new CatalogValidateException('Unavailable language '.$language );
        }

        $langCode = $this->languagesMap[$language];


        // 1) load all data from database
        $productsLanguages = $this->catalogDao->batchGetProductsLangsWithSubobjects($skus, $langCode, self::STEP );

        // 1.5) load assotiated objects
//        $this->categoryDao->getProductCategories()
        //  pictures
        $productsPictures = $this->picturesDao->batchGetProductsPictures($skus, self::STEP);

        /** @var ProductPicture[][] $productsPicturesArraysMap */
        $productsPicturesArraysMap = [];
        foreach ($skus as $sku ) {
            $productsPicturesArraysMap[$sku] = [];
        }
        foreach ($productsPictures as $pp ) {
            $path = $this->picturesSevice->calculatePicturePath($pp->getPicture()->getId(), $pp->getPicture()->getName());
            $pp->getPicture()->setConfiguredPath( $path );
            $productsPicturesArraysMap[$pp->getProduct()->getSku()][] = $pp;
        }

        // categories
        $productsCategories = $this->categoryDao->batchGetProductsCategories($skus, self::STEP);
        $categoriesCodes = array_map( [ProductCategory::class, 'lambdaGetCategoryCode'] , $productsCategories);

        // categoriesLanguages
        $categoriesLanguages = $this->categoryDao->batchGetCategoriesLanguages( $categoriesCodes, $langCode, self::STEP );

        $categoriesLanguagesMap = [];
        foreach ($categoriesLanguages as $cl) {
            $categoriesLanguagesMap[$cl->getCode()] = $cl;
        }

        /** @var CategoryLanguage[][] $productsCategoriesArraysMap */
        $productsCategoriesArraysMap = [];
        foreach ($skus as $sku ) {
            $productsCategoriesArraysMap[$sku] = [];
        }

        foreach ($productsCategories as $pc ) {
            $sku = $pc->getProduct()->getSku();
            $code = $pc->getCategory()->getCode();
            if ( array_key_exists($code, $categoriesLanguagesMap) ) {
                $cl = $categoriesLanguagesMap[$code];
                $productsCategoriesArraysMap[$sku][] = $cl;
            }
        }

        // classificators
        $products = array_map ( [ProductLanguage::class, 'lambdaGetProduct'], $productsLanguages);
        $classificatorsCodes = ProductsHelper::getAllClassificatorsCodes($products);
        $classificatorsCodes = array_unique($classificatorsCodes);

        // load classificators languages from database
        $classificatorsLanguages = $this->catalogDao->loadClassificatorsLanguagesByCodes($classificatorsCodes, $langCode); // TODO make by parts

        /** @var ClassificatorLanguage[] $classificatorsLanguagesMap */
        $classificatorsLanguagesMap = [];
        foreach ($classificatorsLanguages as $cl ) {
            $classificatorsLanguagesMap[$cl->getClassificator()->getCode()] = $cl;
        }

        // 2) build legacy structure for returning
        /** @var KatalogasPreke[] $prekes */
        $prekes = [];

        foreach ( $productsLanguages as $productLang ) {
            /** @var ClassificatorLanguage[] $productClassificatorsLanguages */
            $productClassificatorsLanguagesByGroupsMap = [];
            foreach (Product::CLASSIFICATORS_GROUPS as $group ) {
                // 1) get classificator by group
                /** @var Classificator $c */
                $c = $productLang->getProduct()->{'get'.$group}();
                if ( $c != null ) {
                    // 2) get classificator language from map
                    if ( array_key_exists( $c->getCode(), $classificatorsLanguagesMap )) {
                        $productClassificatorsLanguagesByGroupsMap[$group] = $classificatorsLanguagesMap[$c->getCode()];
                    }
                }
            }
            $preke = ProductToKatalogasPrekeMapper::mapProduct2KatalogasPreke($productLang,
                $productsCategoriesArraysMap[$productLang->getSku()],
                $productsPicturesArraysMap[$productLang->getSku()],
                $productClassificatorsLanguagesByGroupsMap);
            $prekes[] = $preke;
        }
        return $prekes;
    }

    /**
     * Called from DI initializaotor (yml)
     * @param array $languagesMap
     */
    public function setLanguagesMap(array $languagesMap): void
    {
        $this->languagesMap = $languagesMap;
    }
}