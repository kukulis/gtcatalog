<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 23.07
 */

namespace Gt\Catalog\Services;


use Doctrine\ORM\ORMException;
use Gt\Catalog\CsvUtils;
use Gt\Catalog\Dao\CatalogDao;
use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Data\ProductsFilter;
use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\Language;
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

    public function importProducts(  $csvFile ) {
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

        $partSize = 100;

        for ( $i = 0; $i < count($lines); $i+= $partSize) {

            $part = array_slice($lines, $i, $partSize);

            /** @var Product[] $products */
            $products = [];

            /** @var ProductLanguage[] $productsLangs */
            $productsLangs = [];

            // make array for importing product

            foreach ($part as $l) {
                // convert array line to assoc line
                $line = CsvUtils::arrayToAssoc($headMap, $l);

                $product = new Product();
                $product->setSku($line['sku']);
                $product->setBrand($line['brand']);
                $product->setLine($line['line']);
                $product->setParentSku($line['parentSku']);
                $product->setOriginCountryCode($line['originCountryCode']);
                $product->setVendor($line['vendor']);
                $product->setManufacturer($line['manufacturer']);
                $product->setType($line['type']);
                $product->setPurpose($line['purpose']);
                $product->setMeasure($line['measure']);
                $product->setColor($line['color']);
                $product->setForMale($line['forMale']);
                $product->setForFemale($line['forFemale']);
                $product->setSize($line['size']);
                $product->setPackSize($line['packSize']);
                $product->setPackAmount($line['packAmount']);
                $product->setWeight($line['weight']);
                $product->setLength($line['length']);
                $product->setHeight($line['height']);
                $product->setWidth($line['width']);
                $product->setDeliveryTime($line['deliveryTime']);

                $products[] = $product;
                // TODO validate each value classificator ?

                // make array for importing productLanguages
                $productLang = new ProductLanguage();
                $productLang->setProduct($product);
                $language = new Language();
                $language->setCode($line['language']);
                $productLang->setLanguage($language);
                $productLang->setName($line['name']);
                $productLang->setDescription($line['description']);
                $productLang->setLabel($line['label']);
                $productLang->setVariantName($line['variantName']);
                $productLang->setInfoProvider($line['infoProvider']);
                $productLang->setTags($line['tags']);

                $productsLangs [] = $productLang;
            }

            // TODO validate for a whole part of products

            // TODO collect classificators for each group
            // TODO validate each group of classificators

            $this->catalogDao->importProducts ( $products, $headMap);
            $this->catalogDao->importProductsLangs( $productsLangs, $headMap );
        }
        return 0;
    }
}