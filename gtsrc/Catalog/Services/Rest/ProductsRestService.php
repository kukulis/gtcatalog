<?php

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
use Gt\Catalog\Repository\PackageTypeRepository;
use Gt\Catalog\Services\PicturesService;
use Gt\Catalog\Transformer\CategoryTransformer;
use Gt\Catalog\Transformer\ProductTransformer;
use Gt\Catalog\Utils\BatchRunner;
use Gt\Catalog\Utils\MapBuilder;
use Gt\Catalog\Utils\ProductsHelper;
use Psr\Log\LoggerInterface;

class ProductsRestService
{
    private const MAX_PORTION = 500;
    private const STEP = 100;

    private LoggerInterface $logger;
    private CatalogDao $catalogDao;
    private CategoryDao $categoryDao;

    // TODO unused service
//    private CategoriesService $categoriesService;
    private LanguageDao $languageDao;
    private PicturesDao $picturesDao;
    private PicturesService $picturesService;
    private ProductTransformer $productTransformer;
    private PackageTypeRepository $packageTypeRepository;

    private array $languagesMap = [];

    public function __construct(
        LoggerInterface $logger,
        CatalogDao $catalogDao,
        CategoryDao $categoryDao,
//        CategoriesService $categoriesService,
        PicturesDao $picturesDao,
        PicturesService $picturesService,
        LanguageDao $languageDao,
        ProductTransformer $productTransformer,
        PackageTypeRepository $packageTypeRepository
    ) {
        $this->logger = $logger;
        $this->catalogDao = $catalogDao;
        $this->categoryDao = $categoryDao;
//        $this->categoriesService = $categoriesService;
        $this->languageDao = $languageDao;
        $this->picturesDao = $picturesDao;
        $this->picturesService = $picturesService;
        $this->productTransformer = $productTransformer;
        $this->packageTypeRepository = $packageTypeRepository;
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

        $additionalProductLanguages = BatchRunner::runBatchArrayResult(
            $skus,
            100,
            fn($part) => $this->catalogDao->loadProductLanguagesLazy($part, $addidionalLanguages),
            fn($msg) => $this->logger->info($msg)
        );

        $additionalLanguageData = self::buildAdditionalNameLanguageData($additionalProductLanguages);

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
            $path = $this->picturesService->calculatePicturePath(
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
    public static function buildAdditionalNameLanguageData(array $productLanguages): array
    {
        $result = [];

        foreach ($productLanguages as $pl) {
            $sku = $pl->getProduct()->getSku();

            if (!array_key_exists($pl->getProduct()->getSku(), $result)) {
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

        $languages = $this->languageDao->getLanguagesList(0, 100);
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
     * @return \Catalog\B2b\Common\Data\Catalog\Product[]
     */
    public function getRestProducts(array $skus, string $language): array
    {
        // TODO get product data even if it has no translaton
        $productsByLanguage = $this->getProductsLanguages($skus, $language);

        $transformedProducts = array_map(
            fn($pl) => $this->productTransformer->transformToRestProduct($pl),
            $productsByLanguage
        );

        $this->assignCategories($transformedProducts, $language);

        return $transformedProducts;
    }

    /**
     * @param \Catalog\B2b\Common\Data\Catalog\Product[] $products
     */
    private function assignCategories(array $products, string $language)
    {
        $skus = array_map(fn($p) => $p->sku, $products);
        $productsCategories = $this->categoryDao->getProductsCategories($skus);

        $categoriesCodes = array_unique(array_map(fn($pc) => $pc->getCategory()->getCode(), $productsCategories));
        $categoriesLanguages = $this->categoryDao->getCategoriesLanguages($categoriesCodes, $language);

        /** @var CategoryLanguage[] $categoriesLanguagesMap indexed by category code */
        $categoriesLanguagesMap = MapBuilder::buildMap(
            $categoriesLanguages,
            fn(CategoryLanguage $cl) => $cl->getCode()
        );


        $restProductsMap = MapBuilder::buildMap($products, fn(\Catalog\B2b\Common\Data\Catalog\Product $p) => $p->sku);

        foreach ($productsCategories as $productCategory) {
            if (!array_key_exists($productCategory->getProduct()->getSku(), $restProductsMap)) {
                // If impossible then delete this 'if' block
                $this->logger->error(
                    sprintf(
                        'No rest product found for sku %s (category %s)',
                        $productCategory->getProduct()->getSku(),
                        $productCategory->getCategory()->getCode()
                    )
                );
                continue;
            }

            $restProduct = $restProductsMap[$productCategory->getProduct()->getSku()];

            if (!array_key_exists($productCategory->getCategory()->getCode(), $categoriesLanguagesMap)) {
                $this->logger->error(
                    sprintf(
                        'No category language found for category %s language %s product %s ',
                        $productCategory->getCategory()->getCode(),
                        $language,
                        $productCategory->getProduct()->getSku(),

                    )
                );
                continue;
            }

            $cl = $categoriesLanguagesMap[$productCategory->getCategory()->getCode()];
            $restProduct->categories[] = CategoryTransformer::transformToRest($cl);
        }
    }

    /**
     * @param \Catalog\B2b\Common\Data\Catalog\Product[] $dtoProducts
     */
    public function updateSpecial(array $dtoProducts): int
    {
        $skus = array_map(fn($p) => $p->sku, $dtoProducts);

        $products = $this->catalogDao->loadProductsBySkus($skus);

        /** @var Product[] $productsIndexed */
        $productsIndexed = MapBuilder::buildMap($products, fn(Product $product) => $product->getSku());

        $packagesTypes = $this->packageTypeRepository->findAll();

        $updatedProducts = [];
        foreach ($dtoProducts as $dtoProduct) {
            if (!array_key_exists($dtoProduct->sku, $productsIndexed)) {
                continue;
            }
            $dbProduct = $productsIndexed[$dtoProduct->sku];
            $fieldsToUpdate = ProductTransformer::updateSpecialProduct($dtoProduct, $dbProduct, $packagesTypes);
            if (count($fieldsToUpdate) > 0) {
                $this->logger->debug(
                    sprintf(
                        'For product [%s] fields will be updated : [%s]',
                        $dbProduct->getSku(),
                        join(',', $fieldsToUpdate)
                    )
                );

                $updatedProducts[] = $dbProduct;
            }
        }

        $this->catalogDao->updateMultipleProducts($updatedProducts);

        return count($updatedProducts);
    }
}