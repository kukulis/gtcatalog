<?php

namespace Gt\Catalog\Services;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\ORMException;
use Gt\Catalog\Dao\CategoryDao;
use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Data\CategoriesFilter;
use Gt\Catalog\Entity\Category;
use Gt\Catalog\Entity\CategoryLanguage;
use Gt\Catalog\Entity\Language;
use Gt\Catalog\Entity\ProductCategory;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Utils\CategoriesHelper;
use Gt\Catalog\Utils\CsvUtils;
use Psr\Log\LoggerInterface;
use Sketis\B2b\Common\Helper\Utils;

class CategoriesService extends ProductsBaseService
{
    const PAGE_SIZE = 10;

    const DEFAULT_LANGUAGE_CODE = 'en';

    const STEP = 100;

    /** @var LoggerInterface */
    private $logger;

    /** @var LanguageDao */
    private $languageDao;

    /**
     * CategoriesService constructor.
     * @param LoggerInterface $logger
     * @param CategoryDao $categoryDao
     */
    public function __construct(LoggerInterface $logger,
                                CategoryDao $categoryDao,
                                LanguageDao $languageDao)
    {
        $this->logger = $logger;
        $this->categoryDao = $categoryDao;
        $this->languageDao = $languageDao;
    }

    /**
     * @param CategoriesFilter $filter
     * @return CategoryLanguage[]
     * @throws \Gt\Catalog\Exception\CatalogErrorException
     */
    public function getCategoriesLanguages(CategoriesFilter $filter)
    {
        $categories = $this->categoryDao->getCategories($filter);
        $codes = array_map([Category::class, 'lambdaGetCode'], $categories);

        $languageCode = self::DEFAULT_LANGUAGE_CODE;
        if ($filter->getLanguage() != null) {
            $languageCode = $filter->getLanguage()->getCode();
        }

        $categoriesLanguages = $this->categoryDao->getCategoriesLanguages($codes, $languageCode);

        /** @var CategoryLanguage[] $clMap */
        $clMap = [];

        foreach ($categoriesLanguages as $cl) {
            $clMap[$cl->getCategory()->getCode()] = $cl;
        }

        /** @var CategoryLanguage[] $clResult */
        $clResult = [];

        $language = $this->languageDao->getLanguage($languageCode);
        foreach ($categories as $c) {
            if (array_key_exists($c->getCode(), $clMap)) {
                $cl = $clMap[$c->getCode()];
            } else {
                $cl = new CategoryLanguage();
                $cl->setCategory($c);
                $cl->setLanguage($language);
            }
            $clResult[] = $cl;
        }
        return $clResult;
    }

    /**
     * @param string $code
     * @param string $languageCode
     * @return CategoryLanguage
     * @throws CatalogErrorException
     * @throws CatalogValidateException
     */
    public function getCategoryLanguage($code, $languageCode)
    {
        $categoryLanguage = null;
        try {
            $categoryLanguage = $this->categoryDao->getCategoryLanguage($code, $languageCode);
        } catch (NoResultException $e ) {
            // ok
            $this->logger->notice( 'No category language found with code '.$code. ' language '.$languageCode);
        } catch ( ORMException $e ) {
            throw new CatalogErrorException( $e->getMessage() );
        }

        if (empty($categoryLanguage)) {
            $category = $this->categoryDao->getCategory($code);
            if (empty ($category)) {
                throw new CatalogValidateException('Can\'t load category by code ' . $code);
            }
            $language = $this->languageDao->getLanguage($languageCode);
            if (empty($language)) {
                throw new CatalogValidateException('Can\'t load language by code ' . $languageCode);
            }

            $categoryLanguage = new CategoryLanguage();
            $categoryLanguage->setCategory($category);
            $categoryLanguage->setLanguage($language);
        }
        return $categoryLanguage;
    }

    /**
     * @param CategoryLanguage $cl
     * @return CategoryLanguage
     * @throws CatalogValidateException
     */
    public function storeCategoryLanguage( CategoryLanguage $cl) {
        $this->assignAssociations($cl->getCategory());
        return $this->categoryDao->storeCategoryLanguage($cl);
    }

    /**
     * @return Language[]
     */
    public function getAllLanguages() {
        $languages = $this->languageDao->getLanguagesList(0,10);
        return $languages;
    }

    /**
     * @param Category $c
     * @return Category
     * @throws CatalogValidateException
     */
    public function assignAssociations(Category  $c) {
        if ( !empty( $c->getParentCode() ) ) {
            $parentCategory = $this->categoryDao->getCategory($c->getParentCode());
            if ( $parentCategory == null ) {
                throw new CatalogValidateException('There is no category with code '.$c->getParentCode() );
            }
            $c->setParent($parentCategory);
        }
        return $c;
    }

    /**
     * @param string  $file
     * @return int
     * @throws CatalogValidateException
     */
    public function importCategories ( $file, $updateOnly ) {
        $f = fopen ( $file, 'r');

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

        $this->logger->debug ( 'read lines count '.count($lines) );

        if ( $updateOnly ) {
            $this->categoryDao->setAllConfirmed();
        }

        $count = 0;
        for ( $i =0; $i < count($lines); $i += self::STEP ) {
            $part = array_slice ( $lines, $i, self::STEP);
            $count += $this->importCategoriesData($part, $headMap );
        }

        if ( $updateOnly ) {
            $deletedCount = $this->categoryDao->deleteUnfonfirmedCategoriesLanguages();
            $this->categoryDao->deleteUnfonfirmedCategories();
            $this->logger->debug('Deleted '.$deletedCount.' unconfirmed categories' );
        }

        return $count;
    }

    /**
     * @param string[][] $lines
     * @param int[] $headMap indexes are strings
     * @return int
     * @throws CatalogValidateException
     * @throws CatalogErrorException
     */
    public function importCategoriesData( $lines, $headMap ) {
        /** @var Category[] $categories */
        $categories = [];

        /** @var CategoryLanguage[] $categoriesLanguages */
        $categoriesLanguages=[];

        foreach ( $lines as $l ) {
            if ( count($l) != count($headMap)) {
                throw new CatalogValidateException('Wrong amount of elements in line ['.join ( ',',$l ).'] = '.count($l).', while '.count($headMap). ' is needed');
            }
            $l = array_map ( 'trim', $l );

            $line = CsvUtils::arrayToAssoc($headMap, $l);

            $catLang = self::mapCsvLine($line);

            $categories[] = $catLang->getCategory();
            $categoriesLanguages[] = $catLang;
        }

        try {
            $catCount = $this->categoryDao->importCategories($categories, $headMap);
            $catLangCount = $this->categoryDao->importCategoriesLanguages($categoriesLanguages, $headMap);

            return max($catCount, $catLangCount);

        } catch ( ForeignKeyConstraintViolationException $e ) {
            $msg = $e->getMessage();
            if ( strpos($msg,'FOREIGN KEY (`parent`)') !== false  ) {
                throw new CatalogValidateException(
                    'Neteisinga parent reikšmė. 
                    Detalės: '.$msg);
            }
            throw new CatalogValidateException( $e->getMessage() );
        } catch (DBALException $e ) {
            $msg = $e->getMessage();
            throw new CatalogErrorException( $msg);
        }
    }

    /**
     * @param $line
     * @return CategoryLanguage
     * @throws CatalogValidateException
     */
    public static function mapCsvLine($line ) {
        $category = new Category();
        $code = $line['code'];

        $parentCode = null;
        if ( isset($line['parent']) ) {
            if ( $line['parent'] == ''  or $line['parent'] == '-') {
                $parentCode = null;
            } else {
                $parentCode = $line['parent'];
            }
        }

        if ( $parentCode != null ) {
            if ( !CategoriesHelper::validateCategoryCode($parentCode) ) {
                throw new CatalogValidateException('Invalid parent code:['.$parentCode.']');
            }
        }

        if ( !CategoriesHelper::validateCategoryCode($code) ) {
            throw new CatalogValidateException('Invalid category code:['.$code.']');
        }

        $category->setCode( $code );
        if ( $parentCode != null ) {
            $category->setParent(Category::createCategory($parentCode));
        }
        else {
            $category->setParent(null);
        }

        if ( array_key_exists('customs_code', $line ) ) {
            $customsCode = $line['customs_code'];
            if ( !empty($customsCode) ) {
                if (!CategoriesHelper::validateCustomsCode($customsCode)) {
                    throw new CatalogValidateException('Wrong customs code: ' . $customsCode);
                }
            }
            $category->setCustomsCode($customsCode);
        }

        $lang = new Language();
        $lang->setCode($line['language']);

        $catLang = new CategoryLanguage();
        $catLang->setCategory($category);
        $catLang->setLanguage($lang);
        if ( isset($line['name']) ) {
            $catLang->setName($line['name']);
        }
        if ( isset($line['description'])) {
            $catLang->setDescription($line['description']);
        }

        return $catLang;
    }

    /**
     * @param array $head
     * @throws CatalogValidateException
     */
    public function validateHead ( $head ) {
        $categoryAndLanguageFields = array_merge(Category::ALLOWED_FIELDS, CategoryLanguage::ALLOWED_FIELDS);
        $nonValidFields = array_diff ( $head, $categoryAndLanguageFields );

        if ( count($nonValidFields) > 0 ) {
            throw new CatalogValidateException('Non valid fields:'.join(',', $nonValidFields));
        }

        $requiredFields = ['code', 'language' ];

        $missingFields = array_diff($requiredFields, $head );
        if ( count($missingFields) > 0 ) {
            throw new CatalogValidateException('Missing fields:'.join(',', $missingFields));
        }
    }

    public function getProductCategories ( $sku ) {
        return $this->categoryDao->getProductCategories($sku);
    }

    /**
     * @param ProductCategory[] $productCategories
     * @return string
     */
    public function getProductCategoriesCodesStr ($productCategories) {
        $codes = array_map ([ProductCategory::class, 'lambdaGetCategoryCode'], $productCategories);
        return join ( " ", $codes );
    }

    /**
     * @param string $sku
     * @param string $categoriesStr
     * @return int
     * @throws CatalogErrorException
     * @throws CatalogValidateException
     */
    public function updateProductCategories( $sku, $categoriesStr ) {
        $codes = CategoriesHelper::splitCategoriesStr($categoriesStr);
        $this->validateExistingCategories($codes);

        /** @var ProductCategory[] $productCategories */
        $productCategories = [];

        foreach ($codes as $code ) {
             $productCategories[] = ProductCategory::create($sku, $code);
        }

        try {
            $this->categoryDao->markDeletedProductCategories([$sku]);
            $count = $this->categoryDao->importProductCategories($productCategories);
            $this->categoryDao->deleteMarkedProductCategories();

            return $count;
        } catch ( DBALException $e ) {
            throw new CatalogErrorException( $e->getMessage() );
        }
    }
}