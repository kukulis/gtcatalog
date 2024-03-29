<?php

namespace Gt\Catalog\Dao;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Data\CategoriesFilter;
use Gt\Catalog\Entity\Category;
use Gt\Catalog\Entity\CategoryLanguage;
use Gt\Catalog\Entity\ProductCategory;
use Gt\Catalog\Utils\MapBuilder;
use Psr\Log\LoggerInterface;

class CategoryDao extends BaseDao
{
    const MAX_ROOTS = 1000;

    /** @var LoggerInterface */
    private $logger;

    /** @var Registry */
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
    public function getCategories(CategoriesFilter $filter)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $builder = $em->createQueryBuilder();
        $builder->select('c')
            ->from(Category::class, 'c')
        ;
        $builder->leftJoin('c.parent', 'p');

        if (!empty($filter->getLikeCode())) {
            $builder->andWhere('c.code like :likeCode');
            $builder->setParameter('likeCode', '%' . $filter->getLikeCode() . '%');
        }

        if (!empty($filter->getLikeParent())) {
            $builder->andWhere('p.code like :likeParent');
            $builder->setParameter('likeParent', '%' . $filter->getLikeParent() . '%');
        }

        if (!empty($filter->getExactParent())) {
            $builder->andWhere('p.code = :exactParent');
            $builder->setParameter('exactParent', $filter->getExactParent());
        }

        $builder->orderBy('c.code');

        $builder->setMaxResults($filter->getLimit());
        $builder->setFirstResult($filter->getOffset());

        /** @var Category[] $categories */
        $categories = $builder->getQuery()->getResult();

        return $categories;
    }

    /**
     * @param string[] $categoriesCodes
     * @param string $languageCode
     * @return CategoryLanguage[]
     */
    public function getCategoriesLanguages($categoriesCodes, $languageCode)
    {
        $class = CategoryLanguage::class;
        $dql = /** @lang DQL */
            "SELECT cl from $class cl join cl.category c join cl.language l 
            where c.code in (:codes) and l.code = :language_code";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);

        $query->setParameter('codes', $categoriesCodes);
        $query->setParameter('language_code', $languageCode);

        /** @var CategoryLanguage[] $categoriesLanguages */
        $categoriesLanguages = $query->getResult();

        return $categoriesLanguages;
    }

    /**
     * @param Category[] $categories
     * @param string $languageCode
     * @return CategoryLanguage[]
     */
    public function loadCategoriesLanguages($categories, $languageCode): array
    {
        $categoriesCodes = array_map(fn($c) => $c->getCode(), $categories);

        $cls = $this->getCategoriesLanguages($categoriesCodes, $languageCode);
        $clsMap = MapBuilder::buildMap($cls, fn($cl) => $cl->getCode());

        $resultCategoriesLanguages = [];
        foreach ($categories as $c) {
            if (array_key_exists($c->getCode(), $clsMap)) {
                $cl = $clsMap[$c->getCode()];
            } else {
                $cl = new CategoryLanguage();
                $cl->setCategory($c);
            }

            $resultCategoriesLanguages[] = $cl;
        }

        return $resultCategoriesLanguages;
    }

    /**
     * @param string[] $categoriesCodes
     * @param string $languageCode
     * @param int $step
     * @return CategoryLanguage[]
     */
    public function batchGetCategoriesLanguages($categoriesCodes, $languageCode, $step)
    {
        /** @var CategoryLanguage[] $categoriesLanguagesTotal */
        $categoriesLanguagesTotal = [];

        for ($i = 0; $i < count($categoriesCodes); $i += $step) {
            $part = array_slice($categoriesCodes, $i, $step);
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
    public function getCategoryLanguage($code, $languageCode)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $class = CategoryLanguage::class;

        $dql = /** @lang DQL */
            "SELECT cl FROM $class cl 
                  JOIN cl.category c JOIN cl.language l 
              WHERE c.code=:code and l.code=:languageCode";
        $query = $em->createQuery($dql);
        $query->setParameter('code', $code);
        $query->setParameter('languageCode', $languageCode);

        /** @var CategoryLanguage $categoryLanguage */
        $categoryLanguage = $query->getSingleResult();

        return $categoryLanguage;
    }

    /**
     * @param string $code
     * @return Category
     */
    public function getCategory($code)
    {
        $em = $this->doctrine->getManager();
        /** @var Category $category */
        $category = $em->find(Category::class, $code);
        return $category;
    }

    /**
     * @param CategoryLanguage $cl
     */
    public function storeCategoryLanguage(CategoryLanguage $cl)
    {
        $em = $this->doctrine->getManager();
        $em->persist($cl);
        $em->flush();
        return $cl;
    }

    /**
     * @param Category[] $categories
     * @return int
     *
     */
    public function importCategories($categories, $givenFieldsSet)
    {
        $givenFields = array_keys($givenFieldsSet);
        $importingFields = array_intersect($givenFields, Category::ALLOWED_FIELDS);

        $skipUpdates = ['code'];

        $updatingFields = array_diff($importingFields, $skipUpdates);

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();

        $sql = $this->buildImportSql(
            $categories,
            $importingFields,
            $updatingFields,
            $this->getQuoter($conn),
            'code',
            'categories'
        );
        return $conn->exec($sql);
    }

    /**
     * @param CategoryLanguage[] $categoriesLangs
     * @return int
     */
    public function importCategoriesLanguages($categoriesLangs, $givenFieldsSet)
    {
        $givenFields = array_keys($givenFieldsSet);
        $importingFields = array_intersect($givenFields, CategoryLanguage::ALLOWED_FIELDS);
        $importingFields = array_merge($importingFields, ['category']);
        // skip updates
        $skipUpdates = ['category', 'language'];
        $updatingFields = array_diff($importingFields, $skipUpdates);

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();

        $sql = $this->buildImportSql(
            $categoriesLangs,
            $importingFields,
            $updatingFields,
            $this->getQuoter($conn),
            'code',
            'categories_languages'
        );
        return $conn->exec($sql);
    }

    /**
     * @param string[] $codes
     * @return Category[]
     */
    public function loadCategories($codes)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $class = Category::class;

        $dql = /** @lang DQL */
            "SELECT c from $class c where c.code in (:codes)";

        $query = $em->createQuery($dql);
        $query->setParameter('codes', $codes);

        /** @var Category[] $categories */
        $categories = $query->getResult();

        return $categories;
    }

    /**
     * @param string $sku
     * @return ProductCategory[]
     */
    public function getProductCategories($sku)
    {
        $class = ProductCategory::class;
        $dql = /** @lang DQL */
            "SELECT pc from $class pc join pc.product p where p.sku=:sku";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
        $query->setParameter('sku', $sku);

        /** @var ProductCategory[] $productCategories */
        $productCategories = $query->getResult();

        return $productCategories;
    }

    /**
     * @param string[] $skus
     * @return ProductCategory[]
     */
    public function getProductsCategories($skus)
    {
        $class = ProductCategory::class;
        $dql = /** @lang DQL */
            "SELECT pc from $class pc join pc.product p where p.sku in (:skus)";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
        $query->setParameter('skus', $skus);

        /** @var ProductCategory[] $productCategories */
        $productCategories = $query->getResult();

        return $productCategories;
    }

    /**
     * @param string[] $skus
     * @param int $step
     * @return ProductCategory[]
     */
    public function batchGetProductsCategories($skus, $step)
    {
        /** @var ProductCategory[] $productsCategoriesTotal */
        $productsCategoriesTotal = [];

        for ($i = 0; $i < count($skus); $i += $step) {
            $part = array_slice($skus, $i, $step);
            $productsCategories = $this->getProductsCategories($part);
            $productsCategoriesTotal = array_merge($productsCategoriesTotal, $productsCategories);
        }
        return $productsCategoriesTotal;
    }

    /**
     * @param ProductCategory[] $productCategories
     * @return int
     */
    public function importProductCategories($productCategories)
    {
        if (count($productCategories) == 0) {
            return 0;
        }
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();

        $quoter = $this->getQuoter($conn);
        $values = [];
        foreach ($productCategories as $pc) {
            $line = [
                $pc->getProduct()->getSku(),
                $pc->getCategory()->getCode(),
                $pc->getDeleted()
            ];

            $qLine = array_map($quoter, $line);
            $lineStr = '(' . join(',', $qLine) . ')';

            $values[] = $lineStr;
        }

        $valuesStr = join(",\n", $values);

        $sql = /** @lang MySQL */
            "INSERT INTO  products_categories (sku, category, deleted)
            VALUES $valuesStr
            ON DUPLICATE KEY UPDATE deleted=values(deleted)";

        return $conn->exec($sql);
    }

    /**
     * @param string[] $skus
     * @return int
     */
    public function markDeletedProductCategories($skus)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();
        $qSkus = array_map([$conn, 'quote'], $skus);
        $skusStr = join(",", $qSkus);
        $sql = /** @lang MySQL */
            "update products_categories set deleted=1 where sku in ($skusStr)";
        return $conn->exec($sql);
    }

    /**
     * @return int
     */
    public function deleteMarkedProductCategories()
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();
        $sql = /** @lang MySQL */
            "delete from products_categories where deleted=1";
        return $conn->exec($sql);
    }

    public function setAllConfirmed()
    {
        $sql = /** @lang MySQL */
            "update categories set confirmed=1";
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();
        return $conn->exec($sql);
    }

    public function deleteUnfonfirmedCategoriesLanguages()
    {
        $sql = /** @lang MySQL */
            "DELETE  cl FROM categories_languages  cl JOIN categories c on cl.category = c.code  WHERE confirmed != 1 or confirmed is null";
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();
        return $conn->exec($sql);
    }

    public function deleteUnfonfirmedCategories()
    {
        $sql = /** @lang MySQL */
            "DELETE FROM categories WHERE confirmed != 1 or confirmed is null";
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();
        return $conn->exec($sql);
    }

    /**
     * @return Category[]
     */
    public function getAll()
    {
        $class = Category::class;
//        $dql = /** @lang DQL */ "SELECT c FROM $class c WHERE c.confirmed = 1";
        $dql = /** @lang DQL */
            "SELECT c FROM $class c";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);

        /** @var Category[] $categories */
        $categories = $query->getResult();
        return $categories;
    }


    /**
     * @param string $categoryCode
     * @param int $limit
     * @return ProductCategory[]
     */
    public function getCategoriesProducts($categoryCode, $limit)
    {
        $class = ProductCategory::class;
        $dql = /** @lang DQL */
            "SELECT pc, p from $class pc join pc.category c
            JOIN pc.product p  
            where c.code = :categoryCode";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
        $query->setParameter('categoryCode', $categoryCode);
        $query->setMaxResults($limit);

        /** @var ProductCategory[] $productCategories */
        $productCategories = $query->getResult();

        return $productCategories;
    }

    /**
     * @return Category[]
     */
    public function getRootCategories()
    {
        $class = Category::class;
        $dql = /** @lang DQL */
            "SELECT c from $class c
             WHERE c.parent is null";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
//        $query->setMaxResults(self::MAX_ROOTS); // ?? may be not needed


        /** @var Category[] $categories */
        $categories = $query->getResult();

        return $categories;
    }

    /**
     * @param $parentCodes
     * @return Category[]
     */
    public function loadCategoriesByParentCodes($parentCodes)
    {
        $class = Category::class;
        $dql = /** @lang DQL */
            "SELECT c from $class c
             JOIN c.parent p
             WHERE p.code in (:parentCodes)";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
        $query->setParameter('parentCodes', $parentCodes);

        /** @var Category[] $categories */
        $categories = $query->getResult();

        return $categories;
    }

}