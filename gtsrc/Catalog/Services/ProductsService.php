<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 23.07
 */

namespace Gt\Catalog\Services;


use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Gt\Catalog\Dao\CategoryDao;
use Gt\Catalog\Entity\Category;
use Gt\Catalog\Entity\ProductCategory;
use Gt\Catalog\Utils\CategoriesHelper;
use Gt\Catalog\Utils\CsvUtils;
use Gt\Catalog\Dao\CatalogDao;
use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Data\ProductsFilter;
use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\Language;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Exception\CatalogDetailedException;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Exception\RelatedObject;
use Gt\Catalog\Exception\RelatedObjectClassificator;
use Gt\Catalog\Utils\ProductsHelper;
use Gt\Catalog\Utils\PropertiesHelper;
use Psr\Log\LoggerInterface;
use \DateTime;

class ProductsService extends ProductsBaseService
{
    const PAGE_SIZE=10;

    /** @var LoggerInterface */
    protected $logger;

    /** @var CatalogDao */
    protected $catalogDao;

    /**
     * @var LanguageDao
     */
    protected $languageDao;

    /**
     * ProductsService constructor.
     * @param LoggerInterface $logger
     * @param CatalogDao $catalogDao
     * @param LanguageDao $languageDao
     * @param CategoryDao $categoryDao
     */
    public function __construct(LoggerInterface $logger,
                                CatalogDao $catalogDao,
                                LanguageDao $languageDao,
                                CategoryDao $categoryDao)
    {
        $this->logger = $logger;
        $this->catalogDao = $catalogDao;
        $this->languageDao = $languageDao;
        $this->categoryDao = $categoryDao; // from base class
    }

    /**
     * @param int $page
     * @return Product[]
     */
    public function getProducts ( ProductsFilter $filter ) {
        $products = $this->catalogDao->getProductsListByFilter($filter);

        $skus = array_map ([Product::class, 'lambdaGetSku'], $products);
        $productsLanguages = $this->catalogDao->getProductsLangs($skus, $filter->getLanguageCode());

        /** @var ProductLanguage[] $plMap */
        $plMap = [];
        foreach ($productsLanguages as $pl ) {
            $plMap[$pl->getProduct()->getSku()] = $pl;
        }

        foreach ($products as $p ) {
            if ( array_key_exists( $p->getSku(), $plMap )) {
                $p->setExtractedName ( $plMap[$p->getSku()]->getName() );
            }
            else {
                $p->setExtractedName('-');
            }
        }

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

    /**
     * @return Language[]
     */
    public function getAllLanguages() {
        $languages = $this->languageDao->getLanguagesList(0,10);
        return $languages;
    }

    /**
     * @param $csvFile
     * @return int
     * @throws CatalogValidateException
     * @throws CatalogErrorException
     */
    public function importProducts(  $csvFile ) {
        $f = fopen ( $csvFile, 'r' );

        // read head
        $head  = fgetcsv($f);
        $this->validateHead ( $head );

        $headMap = array_flip ( $head );

        $headMapWithLastUpdate = $headMap;
        $headMapWithLastUpdate['last_update'] = -1;

        // read all data to memory

        $lines = [];
        while ( ($line = fgetcsv($f)) != null ) {
            $lines[] = $line;
        }
        fclose($f);

        $givenFields = $head;
        $importingFieldsProducts = array_diff(array_intersect($givenFields, Product::ALLOWED_FIELDS ), Product::CLASSIFICATORS_GROUPS);
        $importingFieldsProductsLangs = array_intersect($givenFields, ProductLanguage::ALLOWED_FIELDS );
        $importingFieldsProductsClassificators = array_intersect($givenFields, Product::CLASSIFICATORS_GROUPS );

        try {
            $productsCount = 0;
            $prodLangCount = 0;
            $partSize = 100;
            for ($i = 0; $i < count($lines); $i += $partSize) {

                $part = array_slice($lines, $i, $partSize);

                /** @var Product[] $products */
                $products = [];

                /** @var ProductLanguage[] $productsLangs */
                $productsLangs = [];

                $productCategories = [];

                // make array for importing product

                // we transform to Product and ProductLanguage arrays, because
                // the functions importProducts and importProductsLangs will be universal for importing data
                // from other sources than csv
                foreach ($part as $l) {

                    // convert array line to assoc line
                    $line = CsvUtils::arrayToAssoc($headMap, $l);

                    $product = new Product();
                    $product->setSku($line['sku']);

                    $this->validateProductSku( $product->getSku() );

                    foreach ($importingFieldsProductsClassificators as $f ) {
                        $setter = 'set'. PropertiesHelper::removeUnderScores($f);
                        $product->$setter(Classificator::createClassificator( strtolower($line[$f]), $f));
                    }

                    foreach ($importingFieldsProducts as $f ) {
                        $setter = 'set'.PropertiesHelper::removeUnderScores($f);
                        $val = $line[$f];
                        if ( $val === '' ) {
                            $val = null;
                        }
                        $product->$setter($val);
                    }

                    $product->setLastUpdate( new DateTime() );

                    $products[] = $product;

                    if ( isset($line['language']) ) {

                        // make array for importing productLanguages
                        $productLang = new ProductLanguage();
                        $productLang->setProduct($product);
                        $language = new Language();
                        $language->setCode($line['language']);
                        $productLang->setLanguage($language);
                        foreach ($importingFieldsProductsLangs as $f ) {
                            $setter = 'set'.PropertiesHelper::removeUnderScores($f);
                            $productLang->$setter($line[$f]);
                        }
                        $productsLangs [] = $productLang;
                    }

                    if ( isset($line['categories'])) {
                        $categoriesStr = $line['categories'];
                        $categoriesArr = CategoriesHelper::splitCategoriesStr( $categoriesStr);
                        $categoriesArr = array_map('strtolower', $categoriesArr);

                        $this->validateCategoriesCodes($categoriesArr, ' product sku '.$product->getSku());

                        foreach ( $categoriesArr as $code ) {
                            $pc = new ProductCategory();
                            $pc->setCategory(Category::createCategory($code));
                            $pc->setProduct($product);

                            $productCategories[] = $pc;
                        }
                    }
                }
                $this->validateClassificators($products);
                $productsCount += $this->catalogDao->importProducts($products, $headMapWithLastUpdate);
                if ( count($productsLangs )) {
                    $prodLangCount += $this->catalogDao->importProductsLangs($productsLangs, $headMap);
                }

                if ( count($productCategories) > 0  ) {
                    $delSkus = [];
                    $catCodes = [];
                    foreach ($productCategories as $pc ) {
                        $delSkus[] = $pc->getProduct()->getSku();
                        $catCodes[] = $pc->getCategory()->getCode();
                    }
                    $delSkus = array_unique($delSkus);
                    $catCodes = array_unique($catCodes);

                    $this->validateExistingCategories($catCodes);

                    $this->categoryDao->markDeletedProductCategories($delSkus);
                    $pcCount = $this->categoryDao->importProductCategories($productCategories);
                    $this->logger->debug('Imported '.$pcCount.' product categories assignments' );
                    $this->categoryDao->deleteMarkedProductCategories();
                }
            }
            return max ( $productsCount, $prodLangCount);
        } catch ( DBALException $e ) {
            throw new CatalogErrorException($e->getMessage());
        }
    }

    /**
     * @param $categoriesArr
     * @param string $context
     * @throws CatalogValidateException
     */
    private function validateCategoriesCodes($categoriesArr, $context='') {
        foreach ($categoriesArr as $code) {
            if ( ! CategoriesHelper::validateCategoryCode($code) ) {
                throw new CatalogValidateException('Invalid category code ['.$code.'] in '.$context );
            }
        }
    }
    /**
     * @param Product[] $products
     * @throws CatalogValidateException
     */
    private function validateClassificators($products) {

        $missingMap = [];
        foreach (Product::CLASSIFICATORS_GROUPS as $cg ) {
            $propeties = PropertiesHelper::getProperties($cg, $products, 'code');
            $classificators = $this->catalogDao->findClassificators($cg, $propeties );
            $dbProperties = PropertiesHelper::getProperties( 'code', $classificators, null);
            $missingProperties = array_diff($propeties, $dbProperties);

            if ( count($missingProperties) > 0  ) {
                $missingMap[$cg] = $missingProperties;
            }
        }

        // -- may be separate to two functions

        if ( count($missingMap) > 0 ) {
            $messages = [];
            foreach ($missingMap as $group => $missingCodes) {
                $msg = 'Missing classificators for group [' . $group . ']  : [' . join(',', $missingCodes) . ']';
                $messages[] = $msg;
            }

            throw new CatalogValidateException(join(";\n", $messages));
        }
    }


    /**
     * @param string $csvFile
     * @throws CatalogErrorException
     * @return int
     */
    public function importClassificatorsFromProductsCsv ($csvFile) {
        $f = fopen ( $csvFile, 'r' );

        // read head
        $head  = fgetcsv($f);
        $headMap = array_flip ( $head );

        // read all data to memory

        $lines = [];
        while ( ($line = fgetcsv($f)) != null ) {
            $lines[] = $line;
        }
        fclose($f);


        $count = 0;
        $importingFieldsProductsClassificators = array_intersect($head, Product::CLASSIFICATORS_GROUPS );
        try {
            $partSize = 100;

            for ($i = 0; $i < count($lines); $i += $partSize) {

                $part = array_slice($lines, $i, $partSize);

                /** @var Classificator[] $classificators */
                $classificators = [];
                foreach ($part as $l) {
                    $line = CsvUtils::arrayToAssoc($headMap, $l);
                    $sku = $line['sku'];
                    foreach ($importingFieldsProductsClassificators as $cg ) {
                        $ccode = $line[$cg];

                        $this->validateClassificatorsCode($ccode, 'for '.$sku.'  group '.$cg );
                        $c = Classificator::createClassificator($ccode, $cg );
                        $classificators[] = $c;

                        $count++;
                    }
                }

                $classificatorsFieldsSet = array_flip (['code', 'group' ]);
                $this->catalogDao->importClassificators($classificators, $classificatorsFieldsSet);
                // langs too without update ?
            }

            return $count;
        } catch ( DBALException $e ) {
            throw new CatalogErrorException($e->getMessage());
        }
    }

    /**
     * @param $head
     * @throws CatalogValidateException
     */
    public function validateHead ( $head ) {
        $productAndLanguageFields = array_merge ( ['sku', 'language', 'categories'], Product::ALLOWED_FIELDS, ProductLanguage::ALLOWED_FIELDS );
        $nonValidFields = array_diff ( $head, $productAndLanguageFields );

        if ( count($nonValidFields) > 0 ) {
            throw new CatalogValidateException('Non valid fields:'.join(',', $nonValidFields));
        }

        $requiredFields = ['sku'];
        $missingFields = array_diff($requiredFields, $head);

        if ( count($missingFields) > 0 ) {
            throw new CatalogValidateException('Missing fields:'.join(',', $missingFields));
        }
    }

    /**
     * @param $sku
     * @throws CatalogValidateException
     */
    private function validateProductSku ( $sku ) {
        if ( ! ProductsHelper::validateProductSku($sku) ) {
            throw new CatalogValidateException('Invalid sku '.$sku );
        }
    }

    /**
     * @param string $code
     * @param string $context
     * @throws CatalogValidateException
     */
    private function validateClassificatorsCode($code, $context) {
         if ( ! CategoriesHelper::validateClassificatorCode($code) ) {
             throw new CatalogValidateException('Invalid classificator code '.$code.'    '.$context );
         }
    }
}