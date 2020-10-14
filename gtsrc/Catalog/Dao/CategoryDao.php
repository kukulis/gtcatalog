<?php


namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Data\CategoriesFilter;
use Gt\Catalog\Entity\Category;
use Gt\Catalog\Entity\CategoryLanguage;
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
}