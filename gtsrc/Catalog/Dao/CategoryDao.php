<?php


namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Data\CategoriesFilter;
use Gt\Catalog\Entity\Category;
use Gt\Catalog\Entity\CategoryLanguage;
use Gt\Catalog\Utils\PropertiesHelper;
use Psr\Log\LoggerInterface;

class CategoryDao
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

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();

        $rows = [];
        foreach ($categories as $cat ) {
            $values = PropertiesHelper::getValuesArray($cat, $importingFields, 'code');
            $qValues = array_map($this->getQuoter($conn), $values);
            $row = '('. join ( ',', $qValues ) . ')';
            $rows[] = $row;
        }

        $valuesStr = join ( ",\n", $rows );
        $fieldsStr = join ( ',', $importingFields);

        $updates=[];
        foreach ($importingFields as $f) {
            if ( array_search( $f, $skipUpdates) !== false ) {
                continue;
            }
            $updateStr = "$f=VALUES($f)";
            $updates[] = $updateStr;
        }

        $updatesInstruction = '';

        if( count($updates) > 0 ) {
            $updatesStr = join(",\n", $updates);

            $updatesInstruction = "ON DUPLICATE KEY UPDATE
                $updatesStr";
        }

        $sql = /** @lang MySQL */
            "INSERT INTO categories ($fieldsStr)
                VALUES $valuesStr
                $updatesInstruction";

        return $conn->exec($sql);
    }

    /**
     * @param CategoryLanguage[] $categoriesLangs
     * @return int
     */
    public function importCategoriesLanguages($categoriesLangs, $givenFieldsSet) {

        // TODO reuse code both in this function and in importCategories

        $givenFields = array_keys($givenFieldsSet);
        $importingFields = array_intersect($givenFields, CategoryLanguage::ALLOWED_FIELDS );
        $importingFields = array_merge( $importingFields, ['category']);

        // skip updates
        $skipUpdates = ['category', 'language' ];

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();

        $rows = [];
        foreach ($categoriesLangs as $catLang ) {
            $values = PropertiesHelper::getValuesArray($catLang, $importingFields, 'code');
            $qValues = array_map($this->getQuoter($conn), $values);
            $row = '('. join ( ',', $qValues ) . ')';
            $rows[] = $row;
        }

        $valuesStr = join ( ",\n", $rows );
        $fieldsStr = join ( ',', $importingFields);

        $updates=[];
        foreach ($importingFields as $f) {
            if ( array_search( $f, $skipUpdates) !== false ) {
                continue;
            }
            $updateStr = "$f=VALUES($f)";
            $updates[] = $updateStr;
        }

        $updatesInstruction = '';

        if( count($updates) > 0 ) {
            $updatesStr = join(",\n", $updates);

            $updatesInstruction = "ON DUPLICATE KEY UPDATE
                $updatesStr";
        }

        $sql = /** @lang MySQL */
            "INSERT INTO categories_languages ($fieldsStr)
                VALUES $valuesStr
                $updatesInstruction";

        return $conn->exec($sql);
    }

    /**
     * @param Connection $con
     * @return \Closure
     */
    public function getQuoter ( Connection $con ) {
        $f = function ($str) use($con) {
            if ( $str == null ) {
                return 'null';
            }
            else {
                return $con->quote($str);
            }
        };
        return $f;
    }
}