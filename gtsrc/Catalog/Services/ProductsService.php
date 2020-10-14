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
use Gt\Catalog\Utils\PropertiesHelper;
use Psr\Log\LoggerInterface;
use \DateTime;

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

                // make array for importing product

                // we transform to Product and ProductLanguage arrays, because
                // the functions importProducts and importProductsLangs will be universal for importing data
                // from other sources than csv
                foreach ($part as $l) {

                    // convert array line to assoc line
                    $line = CsvUtils::arrayToAssoc($headMap, $l);

                    $product = new Product();
                    $product->setSku($line['sku']);

                    foreach ($importingFieldsProductsClassificators as $f ) {
                        $setter = 'set'. PropertiesHelper::removeUnderScores($f);
                        $product->$setter(Classificator::createClassificator( $line[$f], $f));
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
                }
                $this->validateClassificators($products);
                $productsCount += $this->catalogDao->importProducts($products, $headMapWithLastUpdate);
                $prodLangCount += $this->catalogDao->importProductsLangs($productsLangs, $headMap);
            }
            return max ( $productsCount, $prodLangCount);
        } catch ( DBALException $e ) {
            throw new CatalogErrorException($e->getMessage());
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
                    foreach ($importingFieldsProductsClassificators as $cg ) {
                        $c = Classificator::createClassificator($line[$cg], $cg );
                        $classificators[] = $c;

                        $count++;
                    }
                }

                $this->catalogDao->importClassificators($classificators);
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
        $productAndLanguageFields = array_merge ( ['sku', 'language'], Product::ALLOWED_FIELDS, ProductLanguage::ALLOWED_FIELDS );
        $nonValidFields = array_diff ( $head, $productAndLanguageFields );

        if ( count($nonValidFields) > 0 ) {
            throw new CatalogValidateException('Non valid fields:'.join(',', $nonValidFields));
        }
    }
}