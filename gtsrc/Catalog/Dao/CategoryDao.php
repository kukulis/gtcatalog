<?php


namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Data\CategoriesFilter;
use Gt\Catalog\Entity\Category;
use Gt\Catalog\Entity\CategoryLanguage;
use Gt\Catalog\Entity\ProductCategory;
use Psr\Log\LoggerInterface;

class CategoryDao extends BaseDao
{
    /** @var LoggerInterface  */
    private $logger;

    /** @var Registry  */
    private $doctrine;

    /**
     * CategoryDao constructor.
     * @param LoggerInterface $logger
     * @param Registry $doctrine
     */
    public function __construct(LoggerInterface $logger, Registry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    /**
     * @param $data
     */
    public function addCategory($data)
    {
        $em = $this->doctrine->getManager();
        $em->persist($data);
        $em->flush();
    }

    /**
     * @param CategoriesFilter $filter
     * @return Category[]
     */
    public function getCategories(CategoriesFilter  $filter ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $builder =  $em->createQueryBuilder();
        $builder->select('c')
            ->from(Category::class, 'c');
        $builder->leftJoin( 'c.parent', 'p' );

        if ( !empty($filter->getLikeCode()) ) {
            $builder->andWhere('c.code like :likeCode');
            $builder->setParameter('likeCode', '%'.$filter->getLikeCode().'%' );
        }

        if (!empty($filter->getLikeParent())) {
            $builder->andWhere('p.code like :likeParent');
            $builder->setParameter('likeParent', '%'.$filter->getLikeParent().'%' );
        }

        $builder->orderBy('c.code');

        $builder->setMaxResults( $filter->getLimit() );

        /** @var Category[] $categories */
        $categories = $builder->getQuery()->getResult();

        return $categories;
    }

    /**
     * @param string[] $categoriesCodes
     * @param string $languageCode
     * @return CategoryLanguage[]
     */
    public function getCategoriesLanguages($categoriesCodes, $languageCode) {
        $class = CategoryLanguage::class;
        $dql = /** @lang DQL */ "SELECT cl from $class cl join cl.category c join cl.language l 
            where c.code in (:codes) and l.code = :language_code";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);

        $query->setParameter('codes', $categoriesCodes );
        $query->setParameter('language_code', $languageCode );

        /** @var CategoryLanguage[] $categoriesLanguages */
        $categoriesLanguages = $query->getResult();

        return $categoriesLanguages;
    }

    /**
     * @param string[] $categoriesCodes
     * @param string $languageCode
     * @param int $step
     * @return CategoryLanguage[]
     */
    public function batchGetCategoriesLanguages($categoriesCodes, $languageCode, $step) {
        /** @var CategoryLanguage[] $categoriesLanguagesTotal */
        $categoriesLanguagesTotal = [];

        for ( $i = 0; $i < count($categoriesCodes); $i+= $step ) {
            $part = array_slice ( $categoriesCodes, $i, $step);
            $categoriesLanguages = $this->getCategoriesLanguages($part, $languageCode);
            $categoriesLanguagesTotal = array_merge($categoriesLanguagesTotal, $categoriesLanguages);
        }
        return $categoriesLanguagesTotal;
    }

    /**
     * @param $code
     * @param $languageCode
     * @return CategoryLanguage
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getCategoryLanguage ( $code, $languageCode ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $class = CategoryLanguage::class;

        $dql= /** @lang DQL */ "SELECT cl FROM $class cl 
                  JOIN cl.category c JOIN cl.language l 
              WHERE c.code=:code and l.code=:languageCode";
        $query = $em->createQuery($dql);
        $query->setParameter('code', $code );
        $query->setParameter('languageCode', $languageCode );

        /** @var CategoryLanguage $categoryLanguage */
        $categoryLanguage = $query->getSingleResult();

        return $categoryLanguage;
    }

    /**
     * @param string $code
     * @return Category
     */
    public function getCategory ($code) {
        $em = $this->doctrine->getManager();
        /** @var Category $category */
        $category = $em->find(Category::class, $code );
        return $category;
    }

    /**
     * @param CategoryLanguage $cl
     */
    public function storeCategoryLanguage (CategoryLanguage  $cl) {
        $em = $this->doctrine->getManager();
        $em->persist($cl);
        $em->flush();
        return $cl;
    }

    /**
     * @param Category[] $categories
     * @return int
     *
     * @throws DBALException
     */
    public function  importCategories ($categories, $givenFieldsSet) {
        $givenFields = array_keys($givenFieldsSet);
        $importingFields = array_intersect($givenFields, Category::ALLOWED_FIELDS );

        $skipUpdates = ['code'];

        $updatingFields = array_diff( $importingFields, $skipUpdates );

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();

        $sql = $this->buildImportSql($categories, $importingFields, $updatingFields, $this->getQuoter($conn), 'code', 'categories' );
        return $conn->exec($sql);
    }

    /**
     * @param CategoryLanguage[] $categoriesLangs
     * @return int
     * @throws DBALException
     */
    public function importCategoriesLanguages($categoriesLangs, $givenFieldsSet) {
        $givenFields = array_keys($givenFieldsSet);
        $importingFields = array_intersect($givenFields, CategoryLanguage::ALLOWED_FIELDS );
        $importingFields = array_merge( $importingFields, ['category']);
        // skip updates
        $skipUpdates = ['category', 'language' ];
        $updatingFields = array_diff($importingFields, $skipUpdates);

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();

        $sql = $this->buildImportSql($categoriesLangs, $importingFields, $updatingFields, $this->getQuoter($conn), 'code' , 'categories_languages');
        return $conn->exec($sql);
    }

    /**
     * @param string[] $codes
     * @return Category[]
     */
    public function loadCategories ( $codes ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $class = Category::class;

        $dql = /** @lang DQL */  "SELECT c from $class c where c.code in (:codes)";

        $query = $em->createQuery($dql);
        $query->setParameter('codes', $codes );

        /** @var Category[] $categories */
        $categories = $query->getResult();

        return $categories;
    }

    /**
     * @param string $sku
     * @return ProductCategory[]
     */
    public function getProductCategories ( $sku ) {
        $class = ProductCategory::class;
        $dql = /** @lang DQL */ "SELECT pc from $class pc join pc.product p where p.sku=:sku";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
        $query->setParameter('sku', $sku );

        /** @var ProductCategory[] $productCategories */
        $productCategories = $query->getResult();

        return $productCategories;
    }

    /**
     * @param string[] $skus
     * @return ProductCategory[]
     */
    public function getProductsCategories ( $skus ) {
        $class = ProductCategory::class;
        $dql = /** @lang DQL */ "SELECT pc from $class pc join pc.product p where p.sku in (:skus)";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
        $query->setParameter('skus', $skus );

        /** @var ProductCategory[] $productCategories */
        $productCategories = $query->getResult();

        return $productCategories;
    }

    /**
     * @param string[] $skus
     * @param int $step
     * @return ProductCategory[]
     */
    public function batchGetProductsCategories($skus, $step) {
        /** @var ProductCategory[] $productsCategoriesTotal */
        $productsCategoriesTotal = [];

        for ( $i = 0; $i < count($skus); $i+= $step ) {
            $part = array_slice ( $skus, $i, $step);
            $productsCategories = $this->getProductsCategories($part);
            $productsCategoriesTotal = array_merge($productsCategoriesTotal, $productsCategories);
        }
        return $productsCategoriesTotal;
    }

    /**
     * @param ProductCategory[] $productCategories
     * @return int
     * @throws DBALException
     */
    public function importProductCategories($productCategories) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();

        $quoter = $this->getQuoter($conn);
        $values = [];
        foreach ( $productCategories as $pc ) {
            $line = [
                $pc->getProduct()->getSku(),
                $pc->getCategory()->getCode(),
                0
            ];

            $qLine = array_map ( $quoter, $line);
            $lineStr = '('.join ( ',', $qLine).')';

            $values[] = $lineStr;
        }

        $valuesStr = join ( ",\n", $values );

        $sql = /** @lang MySQL */ "INSERT INTO  products_categories (sku, category, deleted)
            VALUES $valuesStr
            ON DUPLICATE KEY UPDATE deleted=values(deleted)";

        return $conn->exec($sql);
    }

    /**
     * @param string[] $skus
     * @return int
     * @throws DBALException
     */
    public function markDeletedProductCategories($skus) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();
        $qSkus = array_map ( [$conn, 'quote'], $skus );
        $skusStr = join ( ",", $qSkus );
        $sql = /** @lang MySQL */  "update products_categories set deleted=1 where sku in ($skusStr)";
        return  $conn->exec($sql);
    }

    /**
     * @return int
     * @throws DBALException
     */
    public function deleteMarkedProductCategories() {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();
        $sql = /** @lang MySQL */  "delete from products_categories where deleted=1";
        return  $conn->exec($sql);
    }

}