<?php
/**
 * ProductsRestService.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-15
 * Time: 15:07
 */

namespace Gt\Catalog\Services\Rest;

use Catalog\B2b\Common\Data\Legacy\Catalog\KatalogasPreke;
use Gt\Catalog\Dao\CatalogDao;
use Gt\Catalog\Dao\CategoryDao;
use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Dao\PicturesDao;
use Gt\Catalog\Entity\CategoryLanguage;
use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\ClassificatorLanguage;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductCategory;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Entity\ProductPicture;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Services\PicturesService;
use Gt\Catalog\Utils\BatchRunner;
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

    /** @var array   initialized dynamicaly */
    private $languagesMap = null;

    /** @var LanguageDao */
    private $languageDao;

    /**
     * ProductsRestService constructor.
     * @param LoggerInterface $logger
     * @param CatalogDao $catalogDao
     * @param CategoryDao $categoryDao
     */
    public function __construct(
        LoggerInterface $logger,
        CatalogDao $catalogDao,
        CategoryDao $categoryDao,
        PicturesDao $picturesDao,
        PicturesService $picturesService,
        LanguageDao $languageDao
    ) {
        $this->logger = $logger;
        $this->catalogDao = $catalogDao;
        $this->categoryDao = $categoryDao;
        $this->picturesDao = $picturesDao;
        $this->picturesSevice = $picturesService;
        $this->languageDao = $languageDao;
    }


    /**
     * @param string[] $skus
     * @param string $language
     * @return KatalogasPreke[]
     * @throws CatalogValidateException
     */
    public function getLegacyPrekes($skus, $language, $addidionalLanguages = [])
    {
        $this->getLanguagesMap();

        if (count($skus) > self::MAX_PORTION) {
            throw new CatalogValidateException('Maximum skus in request is limited to ' . self::MAX_PORTION);
        }

        if (!array_key_exists($language, $this->languagesMap)) {
            throw new CatalogValidateException('Unavailable language ' . $language);
        }

        $langCode = $this->languagesMap[$language];


        // 1) load all data from database
        $productsLanguages = $this->catalogDao->batchGetProductsLangsWithSubobjects($skus, $langCode, self::STEP);

        $additionalLanguageData = BatchRunner::runBatchArrayResult(
            $skus,
            100,
            fn($part) => $this->catalogDao->loadProductLanguagesLazy($part, $addidionalLanguages),
            fn($msg) => $this->logger->info($msg)
        );

        // 1.5) load assotiated objects
//        $this->categoryDao->getProductCategories()
        //  pictures
        $productsPictures = $this->picturesDao->batchGetProductsPictures($skus, self::STEP);

        /** @var ProductPicture[][] $productsPicturesArraysMap */
        $productsPicturesArraysMap = [];
        foreach ($skus as $sku) {
            $productsPicturesArraysMap[$sku] = [];
        }
        foreach ($productsPictures as $pp) {
            $path = $this->picturesSevice->calculatePicturePath(
                $pp->getPicture()->getId(),
                $pp->getPicture()->getName()
            );
            $pp->getPicture()->setConfiguredPath($path);
            $productsPicturesArraysMap[$pp->getProduct()->getSku()][] = $pp;
        }

        // categories
        $productsCategories = $this->categoryDao->batchGetProductsCategories($skus, self::STEP);
        $categoriesCodes = array_map([ProductCategory::class, 'lambdaGetCategoryCode'], $productsCategories);

        // categoriesLanguages
        $categoriesLanguages = $this->categoryDao->batchGetCategoriesLanguages($categoriesCodes, $langCode, self::STEP);

        $categoriesLanguagesMap = [];
        foreach ($categoriesLanguages as $cl) {
            $categoriesLanguagesMap[$cl->getCode()] = $cl;
        }

        /** @var CategoryLanguage[][] $productsCategoriesArraysMap */
        $productsCategoriesArraysMap = [];
        foreach ($skus as $sku) {
            $productsCategoriesArraysMap[$sku] = [];
        }

        foreach ($productsCategories as $pc) {
            $sku = $pc->getProduct()->getSku();
            $code = $pc->getCategory()->getCode();
            if (array_key_exists($code, $categoriesLanguagesMap)) {
                $cl = $categoriesLanguagesMap[$code];
                $productsCategoriesArraysMap[$sku][] = $cl;
            }
        }

        // classificators
        $products = array_map([ProductLanguage::class, 'lambdaGetProduct'], $productsLanguages);
        $classificatorsCodes = ProductsHelper::getAllClassificatorsCodes($products);
        $classificatorsCodes = array_unique($classificatorsCodes);

        // load classificators languages from database
        $classificatorsLanguages = BatchRunner::runBatchArrayResult(
            $classificatorsCodes,
            100,
            fn($part) => $this->catalogDao->loadClassificatorsLanguagesByCodes($part, $langCode),
            fn($msg) => $this->logger->info($msg)
        );

        /** @var ClassificatorLanguage[] $classificatorsLanguagesMap */
        $classificatorsLanguagesMap = [];
        foreach ($classificatorsLanguages as $cl) {
            $classificatorsLanguagesMap[$cl->getClassificator()->getCode()] = $cl;
        }

        // 2) build legacy structure for returning
        /** @var KatalogasPreke[] $prekes */
        $prekes = [];

        foreach ($productsLanguages as $productLang) {
            /** @var ClassificatorLanguage[] $productClassificatorsLanguages */
            $productClassificatorsLanguagesByGroupsMap = [];
            foreach (Product::CLASSIFICATORS_GROUPS as $group) {
                // 1) get classificator by group
                /** @var Classificator $c */
                $c = $productLang->getProduct()->{'get' . $group}();
                if ($c != null) {
                    // 2) get classificator language from map
                    if (array_key_exists($c->getCode(), $classificatorsLanguagesMap)) {
                        $productClassificatorsLanguagesByGroupsMap[$group] = $classificatorsLanguagesMap[$c->getCode()];
                    }
                }
            }

            $categories = [];
            if (array_key_exists($productLang->getSku(), $productsCategoriesArraysMap)) {
                $categories = $productsCategoriesArraysMap[$productLang->getSku()];
            }

            $pictures = [];
            if (array_key_exists($productLang->getSku(), $productsPicturesArraysMap)) {
                $pictures = $productsPicturesArraysMap[$productLang->getSku()];
            }

            $preke = ProductToKatalogasPrekeMapper::mapProduct2KatalogasPreke(
                $productLang,
                $categories,
                $pictures,
                $productClassificatorsLanguagesByGroupsMap
            );

            $this->addAdditionalLanguageData($preke, $additionalLanguageData);

            $prekes[] = $preke;
        }
        return $prekes;
    }

    /**
     * @param KatalogasPreke $preke
     * @param ProductLanguage[] $additionalData
     * @return void
     */
    private function addAdditionalLanguageData(KatalogasPreke $preke, array $additionalData)
    {
        $preke->nameTranslations = [];

        if (array_key_exists($preke->nomnr, $additionalData)) {
            $preke->nameTranslations = $additionalData[$preke->nomnr];
        }
    }

    /**
     * Builds associative array from the ProductLanguage objects array.
     * The results are arrays of arrays, where parent array key is sku, and the inner array key is language code.
     * The inner array value is product name in the given language.
     * @param ProductLanguage[] $productLanguages
     * @return array
     */
    public static function buildAdditionalLanguagesData(array $productLanguages) : array {

        $result = [];

        foreach ($productLanguages as $pl) {
            $sku = $pl->getProduct()->getSku();

            if ( !array_key_exists($pl->getProduct()->getSku(), $result)) {
                $result[$sku] = [];
            }

            $values = &$result[$sku];

            $values[$pl->getLanguage()->getCode()] = $pl->getName();
        }

        return $result;
    }


    public function getLanguagesMap()
    {
        if ($this->languagesMap != null) {
            return $this->languagesMap;
        }

        $languages = $this->languageDao->getLanguagesList(0, 10);
        $this->languagesMap = [];
        foreach ($languages as $l) {
            $this->languagesMap[$l->getCode()] = $l->getCode();
            $this->languagesMap[$l->getLocaleCode()] = $l->getCode();
        }
        return $this->languagesMap;
    }

    /**
     * @param string[] $skus
     * @param string $lang
     * @return ProductLanguage[]
     */
    public function getProductsLanguages($skus, $lang)
    {
        /** @var ProductLanguage[] $rez */
        $rez = [];
        for ($i = 0; $i < self::STEP; $i += self::STEP) {
            $part = array_slice($skus, $i, self::STEP);
            $pls = $this->catalogDao->getProductsLangs($part, $lang);
            $rez = array_merge($rez, $pls);
        }
        return $rez;
    }

    /**
     * @param string[] $skus
     * @param string $lang
     * @return \Catalog\B2b\Common\Data\Catalog\Product[]
     */
    public function getRestProducts($skus, $lang)
    {
        $productsLanguages = $this->getProductsLanguages($skus, $lang);

        /** @var \Catalog\B2b\Common\Data\Catalog\Product[] $rez */
        $rez = [];

        foreach ($productsLanguages as $pl) {
            $p = ProductsHelper::transformToRestProduct($pl);
            $rez [] = $p;
        }
        return $rez;
    }

}