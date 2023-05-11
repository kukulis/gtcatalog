<?php

namespace Gt\Catalog\Dao;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Gt\Catalog\Data\ProductsFilter;
use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\ClassificatorLanguage;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductCategory;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Exception\CatalogDetailedException;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\RelatedObjectClassificator;
use Gt\Catalog\Exception\WrongAssociationsException;
use Psr\Log\LoggerInterface;

class CatalogDao extends BaseDao
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var Registry */
    private $doctrine;

    /**
     * CatalogDao constructor.
     * @param LoggerInterface $logger
     * @param Registry $doctrine
     */
    public function __construct(LoggerInterface $logger, Registry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    /**
     * @param ProductsFilter $filter
     * @return Product[]
     */
    public function getProductsListByFilter(ProductsFilter $filter)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $builder = $em->createQueryBuilder();
        $builder->select('p')
            ->from(Product::class, 'p');

        if (!empty($filter->getLikeSku())) {
            $builder->andWhere('p.sku like :likeSku');
            $builder->setParameter('likeSku', '%' . $filter->getLikeSku() . '%');
        }

        if ( !empty($filter->getCategory())) {
            $builder->join('p.productCategories', 'pc' );
            $builder->join('pc.category', 'c' );
            $builder->andWhere( 'c.code = :categoryCode' );
            $builder->setParameter('categoryCode', $filter->getCategory());
        }

        if (!empty($filter->getBrand())) {
            $builder->andWhere('p.brand like :likeBrand');
            $builder->setParameter('likeBrand', '%' . $filter->getBrand() . '%');
        }

        if (!empty($filter->getDateFrom())) {
            $builder->andWhere('p.lastUpdate >= :dateFrom');
            $builder->setParameter('dateFrom', $filter->getDateFrom());
        }

        if (!empty($filter->getDateTill())) {
            $builder->andWhere('p.lastUpdate <= :dateTill');
            $builder->setParameter('dateTill', $filter->getDateTill());
        }

        $builder->setMaxResults($filter->getLimit());

        /** @var Product[] $products */
        $products = $builder->getQuery()->getResult();

        return $products;
    }

    public function getProductsLangListByFilter(ProductsFilter $filter)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $builder = $em->createQueryBuilder();

        $builder->select('pl, p, l')
            ->from(ProductLanguage::class, 'pl');

        //sku

        $builder->join('pl.product', 'p');
        $builder->join('pl.language', 'l');
        $builder->andWhere( 'l.code = :language');
        $builder->setParameter('language', $filter->getLanguageCode());

        if (!empty($filter->getLikeSku())) {
            $builder->andWhere('p.sku like :likeSku');
            $builder->setParameter('likeSku', '%' . $filter->getLikeSku() . '%');
        }
        if (!empty($filter->getLikeName())) {
            $builder->andWhere('pl.name like :likeName');
            $builder->setParameter('likeName', '%' . $filter->getLikeName() . '%');

        }

        if ( $filter->getLimit() > 0 ) {
            $builder->setMaxResults($filter->getLimit());
        }

        /** @var ProductLanguage[] $productsLanguages */
        $productsLanguages = $builder->getQuery()->getResult();

        return $productsLanguages;
    }

    /**
     * @param string[] $skus
     * @param string $languageCode
     * @return ProductLanguage[]
     */
    public function getProductsLangs($skus, $languageCode)
    {
        $class = ProductLanguage::class;
        $dql = /** @lang DQL */
            "SELECT pl, p from $class pl join pl.product p join pl.language l 
        where p.sku in (:skus) and l.code = :languageCode";
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);

        $query->setParameter('skus', $skus);
        $query->setParameter('languageCode', $languageCode);

        /** @var ProductLanguage[] $productLanguages */
        $productLanguages = $query->getResult();

        return $productLanguages;
    }

    /**
     * @param $skus
     * @param $languageCode
     * @return ProductLanguage[]
     */
    public function getProductsLangsWithSubobjects($skus, $languageCode)
    {
        $class = ProductLanguage::class;
        // kodėl nėra vendor, brand, line, manufacturer ???
        $dql = /** @lang DQL */
            "SELECT pl, p, ty, pur, me, pg 
        from $class pl 
        join pl.product p 
        join pl.language l
        left join p.type ty 
        left join p.purpose pur
        left join p.measure me 
        left join p.productgroup pg
        where p.sku in (:skus) and l.code = :languageCode";
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);

        $query->setParameter('skus', $skus);
        $query->setParameter('languageCode', $languageCode);

        /** @var ProductLanguage[] $productLanguages */
        $productLanguages = $query->getResult();

        return $productLanguages;
    }

    /**
     * @param string[] $skus
     * @param string $languageCode
     * @param int $step
     * @return ProductLanguage[]
     * @deprecated use BatchRunner with lambda function instead.
     */
    public function batchGetProductsLangsWithSubobjects($skus, $languageCode, $step)
    {
        /** @var ProductLanguage[] $productsLanguagesTotal */
        $productsLanguagesTotal = [];
        for ($i = 0; $i < count($skus); $i += $step) {
            $part = array_slice($skus, $i, $step);
            $productsLanguages = $this->getProductsLangsWithSubobjects($part, $languageCode);
            $productsLanguagesTotal = array_merge($productsLanguagesTotal, $productsLanguages);
        }

        return $productsLanguagesTotal;
    }

    /**
     * @param string $sku
     * @return Product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getProduct($sku)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        /** @var Product $product */
        $product = $em->find(Product::class, $sku);

        return $product;
    }

    /**
     * @param Product $p
     * @throws CatalogErrorException
     */
    public function storeProduct(Product $p)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        try {
            $em->persist($p);
        } catch (ORMException $e) {
            throw new CatalogErrorException($e->getMessage());
        }
    }

    /**
     * @throws CatalogErrorException
     */
    public function flush()
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        try {
            $em->flush();
        } catch (ORMException $e) {
            throw new CatalogErrorException($e->getMessage());
        }
    }

    /**
     * @param Product $product
     * @throws CatalogDetailedException
     * @throws CatalogErrorException
     */
    public function assignAssociations(Product $product)
    {
        /** @var Classificator[] $classificatorsToFind */
        $classificatorsToFind = [];

//        $classificatorsToFind[] = $product->getBrand();
//        $classificatorsToFind[] = $product->getLine();
//        $classificatorsToFind[] = $product->getManufacturer();
        $classificatorsToFind[] = $product->getMeasure();
        $classificatorsToFind[] = $product->getPurpose();
        $classificatorsToFind[] = $product->getType();
//        $classificatorsToFind[] = $product->getVendor();
        $classificatorsToFind[] = $product->getProductGroup();

        $classificatorsToFind = array_filter($classificatorsToFind, [$this, 'lambdaNonEmpty']);


        $codes = array_map([Classificator::class, 'lambdaGetCode'], $classificatorsToFind);

        $loadedClassificators = $this->loadClassificatorsList($codes);

        /** @var Classificator[] $loadedMap */
        $loadedMap = [];
        foreach ($loadedClassificators as $c) {
            $loadedMap[$c->getCode()] = $c;
        }

        $messages = [];

        /** @var RelatedObjectClassificator[] $wrongObjects */
        $wrongObjects = [];
        foreach ($classificatorsToFind as $c) {
            if ($c == null || $c->getCode() == null) {
                continue;
            }

            if (!array_key_exists($c->getCode(), $loadedMap)) {
                $messages[] = 'Unfound ' . $c->getCode();

                $relatedObjectClassificator = new RelatedObjectClassificator();
                $relatedObjectClassificator->classificatorCode = $c->getCode();
                $relatedObjectClassificator->correctCode = $c->getGroupCode();
                $wrongObjects[] = $relatedObjectClassificator;
                continue;
            }

            $loadedClassificator = $loadedMap[$c->getCode()];
            if ($loadedClassificator->getGroupCode() != $c->getGroupCode()) {
                $messages[] = 'Group for [' . $loadedClassificator->getCode() . '] is wrong: [' . $loadedClassificator->getGroupCode() . '] should be: [' . $c->getGroupCode() . ']';
                $relatedObjectClassificator = new RelatedObjectClassificator();
                $relatedObjectClassificator->classificatorCode = $c->getCode();
                $relatedObjectClassificator->correctCode = $c->getGroupCode();
                $relatedObjectClassificator->wrongCode = $loadedClassificator->getGroupCode();
                $wrongObjects[] = $relatedObjectClassificator;
                continue;
            }

            if (count($messages) == 0) {
                // čia daromas priskyrimas
                $product->setClassificator($loadedClassificator);
            }
        }

        if (count($messages) > 0) {
            $detailedException = new WrongAssociationsException('Unattached classificators');
            $detailedException->setDetails($messages);
            $detailedException->setRelatedObjects($wrongObjects);

            throw $detailedException;
        }
    }

    /**
     * @param $codes
     * @return Classificator[]
     */
    public function loadClassificatorsList($codes)
    {
        $class = Classificator::class;
        $dql = /** @lang DQL */
            "SELECT c,g FROM $class c join c.classificatorGroup g where c.code in (:codes)";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        /** @var Classificator[] $classificators */
        $classificators = $em->createQuery($dql)->setParameter('codes', $codes)->getResult();

        return $classificators;
    }

    public function lambdaNonEmpty($obj)
    {
        return !empty($obj);
    }

    /**
     * @param string $code
     * @param string $groupCode
     * @param int $limit
     * @return Classificator[]
     */
    public function loadSimmilarClassificators($code, $groupCode, $limit)
    {
        $subCodes = [];
        $subCodes[] = substr($code, 0, 3);
        $subCodes[] = substr($code, 0, 2);
        $subCodes[] = substr($code, 0, 1);
        $subCodes[] = '';

        /** @var Classificator[] $found */
        $found = [];
        foreach ($subCodes as $subCode) {
            $part = $this->loadLikeClassificators($subCode, null, $groupCode, 'en', $limit);
            $found = array_merge($found, $part);
            if (count($found) >= $limit) {
                break;
            }
        }
        return $found;
    }


    /**
     * @param string $likeCode
     * @param string $groupCode
     * @param int $limit
     * @return Classificator[]
     */
    public function loadLikeClassificators($likeCode, $likeName, $groupCode, $language, $limit)
    {
        if (!empty($likeName)) {
            $classificatorsLanguages = $this->loadLikeClassificatorsLanguages(
                $likeCode,
                $likeName,
                $groupCode,
                $language,
                $limit
            );
            $classificators = [];
            foreach ($classificatorsLanguages as $cl) {
                $classificators[] = $cl->getClassificator();
            }
            return $classificators;
        }

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $builder = $em->createQueryBuilder();
        $builder->select('c')
            ->from(Classificator::class, 'c')
            ->join('c.classificatorGroup', 'g');


        if (!empty($groupCode)) {
            $builder->andWhere('g.code=:groupCode');
            $builder->setParameter('groupCode', $groupCode);
        }

        if (!empty($likeCode)) {
            $builder->andWhere('c.code like :likeCode');
            $builder->setParameter('likeCode', '%' . $likeCode . '%');
        }

        $builder->setMaxResults($limit);

        /** @var Classificator[] $classificators */
        $classificators = $builder->getQuery()->getResult();

        return $classificators;
    }

    /**
     * @param string $likeCode
     * @param string $likeName
     * @param string $groupCode
     * @param string $language
     * @param string $limit
     * @return ClassificatorLanguage[]
     */
    public function loadLikeClassificatorsLanguages($likeCode, $likeName, $groupCode, $language, $limit)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $builder = $em->createQueryBuilder();
        $builder->select('cl')
            ->from(ClassificatorLanguage::class, 'cl')
            ->join('cl.classificator', 'c')
            ->join('c.classificatorGroup', 'g')
            ->join('cl.language', 'l');

        $builder->andWhere('l.code=:languageCode');
        $builder->setParameter('languageCode', $language);

        if (!empty($likeName)) {
            $builder->andWhere('cl.name like :likeName');
            $builder->setParameter('likeName', '%' . $likeName . '%');
        }

        if (!empty($groupCode)) {
            $builder->andWhere('g.code=:groupCode');
            $builder->setParameter('groupCode', $groupCode);
        }

        if (!empty($likeCode)) {
            $builder->andWhere('c.code like :likeCode');
            $builder->setParameter('likeCode', '%' . $likeCode . '%');
        }

        $builder->setMaxResults($limit);

        /** @var ClassificatorLanguage[] $cls */
        $cls = $builder->getQuery()->getResult();
        return $cls;
    }

    /**
     * @param Classificator[] $cs
     * @param array $givenFieldsSet
     * @return int
     */
    public function importClassificators($cs, $givenFieldsSet)
    {
        $givenFields = array_keys($givenFieldsSet);
        $importingFields = array_intersect($givenFields, Classificator::ALLOWED_FIELDS);
        // skip updates
        $skipUpdates = ['code', 'classificator_group'];
        $updatingFields = array_diff($importingFields, $skipUpdates);

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();

        $sql = $this->buildImportSql(
            $cs,
            $importingFields,
            $updatingFields,
            $this->getQuoter($conn),
            'code',
            'classificators'
        );
        return $conn->exec($sql);
    }

    /**
     * @param ClassificatorLanguage[] $cls
     * @param array $givenFieldsSet
     * @return int
     */
    public function importClassificatorsLangs($cls, $givenFieldsSet)
    {
        $givenFieldsSet['language'] = 1;
        $givenFields = array_keys($givenFieldsSet);
        $givenFields = array_merge($givenFields, ['classificator']);
        $importingFields = array_intersect($givenFields, ClassificatorLanguage::ALLOWED_FIELDS);
        // skip updates
        $skipUpdates = ['classificator', 'language'];
        $updatingFields = array_diff($importingFields, $skipUpdates);

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();

        $sql = $this->buildImportSql(
            $cls,
            $importingFields,
            $updatingFields,
            $this->getQuoter($conn),
            'code',
            'classificator_lang'
        );
        return $conn->exec($sql);
    }

    /**
     * @param $sku
     * @param $languageCode
     * @return ProductLanguage
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getProductLanguage($sku, $languageCode)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $class = ProductLanguage::class;

        $dql = /** @lang DQL */
            "SELECT pl,p,l from $class pl join pl.product p join pl.language l where p.sku=:sku and l.code=:languageCode";
        $query = $em->createQuery($dql);

        $query->setParameter('sku', $sku);
        $query->setParameter('languageCode', $languageCode);

        /** @var ProductLanguage $productLanguage */
        $productLanguage = $query->getOneOrNullResult();

        return $productLanguage;
    }

    /**
     * @param ProductLanguage $pl
     * @throws CatalogErrorException
     */
    public function storeProductLanguage(ProductLanguage $pl)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        try {
            $em->persist($pl);
        } catch (ORMException $e) {
            throw new CatalogErrorException($e->getMessage());
        }
    }

    /**
     * @param Product[] $products
     * @return int
     */
    public function importProducts($products, $givenFieldsSet)
    {
        // build insert sql script by intersecting possible fields with a given allowedFieldsSet
        $givenFields = array_keys($givenFieldsSet);
        $importingFields = array_intersect($givenFields, Product::ALLOWED_FIELDS);
        $importingFieldsAndSku = array_merge(['sku'], $importingFields);

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();
        $sql = $this->buildImportSql(
            $products,
            $importingFieldsAndSku,
            $importingFields,
            $this->getQuoter($conn),
            'code',
            'products'
        );
        return $conn->exec($sql);
    }

    /**
     * @param ProductLanguage[] $productsLangs
     * @return int
     */
    public function importProductsLangs($productsLangs, $givenFieldsSet)
    {
        $givenFields = array_keys($givenFieldsSet);
        $importingFields = array_intersect($givenFields, ProductLanguage::ALLOWED_FIELDS);
        $importingFieldsAndSkuLang = array_merge(['sku', 'language'], $importingFields);
        // build insert sql script by intersecting possible fields with a given allowedFieldsSet

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();
        $sql = $this->buildImportSql(
            $productsLangs,
            $importingFieldsAndSkuLang,
            $importingFields,
            $this->getQuoter($conn),
            'code',
            'products_languages'
        );
        return $conn->exec($sql);
    }


    /**
     * @param string $group
     * @param string[] $codes
     * @return Classificator[]
     */
    public function findClassificators($group, $codes)
    {
        $class = Classificator::class;
        $dql = /** @lang DQL */
            "SELECT c from  $class c join c.classificatorGroup g where g.code=:groupCode and c.code in (:codes)";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);

        $query->setParameter('groupCode', $group);
        $query->setParameter('codes', $codes);

        /** @var Classificator[] $classificators */
        $classificators = $query->getResult();

        return $classificators;
    }

    /**
     * @param string[] $codes
     * @param string $languageCode
     * @return ClassificatorLanguage[]
     */
    public function loadClassificatorsLanguagesByCodes($codes, $languageCode)
    {
        $class = ClassificatorLanguage::class;
        $dql = /** @lang DQL */
            "SELECT cl from $class cl 
                join cl.classificator c
                join cl.language l 
                where c.code in (:codes) and l.code=:languageCode";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('codes', $codes);
        $query->setParameter('languageCode', $languageCode);

        /** @var ClassificatorLanguage[] $classificatorLanguages */
        $classificatorLanguages = $query->getResult();

        return $classificatorLanguages;
    }

    /**
     * @param string[] $codes
     * @param string $languageCode
     * @param int $step
     * @return ClassificatorLanguage[]
     */
    public function batchLoadClassificatorsLanguagesByCodes($codes, $languageCode, $step)
    {
        /** @var ClassificatorLanguage[] $classificatorLanguagesTotal */
        $classificatorLanguagesTotal = [];

        for ($i = 0; $i < count($codes); $i += $step) {
            $part = array_slice($codes, $i, $step);
            $classificatorLanguages = $this->loadClassificatorsLanguagesByCodes($part, $languageCode);
            $classificatorLanguagesTotal = array_merge($classificatorLanguagesTotal, $classificatorLanguages);
        }
        return $classificatorLanguagesTotal;
    }

    public function getDoctrine(): Registry
    {
        return $this->doctrine;
    }

    /**
     * Currently returning only products names.
     *
     * @param string[] $skus
     * @param string[] $langCodes
     * @return ProductLanguage[]
     */
    public function loadProductLanguagesLazy($skus, $langCodes): array
    {

        if (count($langCodes) == 0) {
            return [];
        }

        /** @var EntityManager $manager */
        $manager = $this->doctrine->getManager();

        $queryBuilder = $manager->createQueryBuilder();

        $queryBuilder->select('pl')->from(ProductLanguage::class, 'pl');
        $queryBuilder->join('pl.product', 'p');
        $queryBuilder->join('pl.language', 'l');
        $queryBuilder->andWhere('p.sku in (:skus)');
        $queryBuilder->setParameter('skus', $skus);

        if ($langCodes[0] != '*') {
            $queryBuilder->andWhere('l.code in (:langs)');
            $queryBuilder->setParameter('langs', $langCodes);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}