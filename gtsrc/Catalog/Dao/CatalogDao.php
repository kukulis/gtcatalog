<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 13.32
 */

namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Gt\Catalog\Data\ProductsFilter;
use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\ClassificatorLanguage;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Exception\CatalogDetailedException;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\RelatedObjectClassificator;
use Gt\Catalog\Exception\WrongAssociationsException;
use Gt\Catalog\Utils\DbHelper;
use Gt\Catalog\Utils\PropertiesHelper;
use Psr\Log\LoggerInterface;

class CatalogDao
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
     * @param int $offset
     * @param int $limit
     * @return Product[]
     */
    public function getProductsList(  $offset, $limit ) {
        $productClass = Product::class;
        $dql = /** @lang DQL */ "SELECT p from $productClass p";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        /** @var Product[] $products */
        $products = $em->createQuery($dql)->setMaxResults($limit)->setFirstResult($offset)->execute();

        return $products;
    }

    public function getProductsListByFilter ( ProductsFilter $filter ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $builder =  $em->createQueryBuilder();
        $builder->select('p')
            ->from(Product::class, 'p');

        if ( !empty($filter->getLikeName() )) {
            $builder->andWhere( 'p.name like :likeName' );
            $builder->setParameter( 'likeName', '%'. $filter->getLikeName().'%' );
        }

        if ( !empty($filter->getLikeSku() ) ) {
            $builder->andWhere('p.sku like :likeSku');
            $builder->setParameter('likeSku', '%'.$filter->getLikeSku().'%' );
        }

        $builder->setMaxResults( $filter->getLimit() );

        /** @var Product[] $products */
        $products = $builder->getQuery()->getResult();

        return $products;
    }

    /**
     * @param string[] $skus
     * @param string $languageCode
     * @return ProductLanguage[]
     */
    public function getProductsLangs ( $skus, $languageCode ) {
        $class = ProductLanguage::class;
        $dql = /** @lang DQL */  "SELECT pl from $class pl join pl.product p join pl.language l 
        where p.sku in (:skus) and l.code = :languageCode";
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);

        $query->setParameter('skus', $skus );
        $query->setParameter('languageCode', $languageCode );

        /** @var ProductLanguage[] $productLanguages */
        $productLanguages = $query->getResult();

        return $productLanguages;
    }

    /**
     * @param string $sku
     * @return Product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function getProduct( $sku ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        /** @var Product $product */
        $product = $em->find(Product::class, $sku );

        return $product;
    }

    /**
     * @param Product $p
     * @throws CatalogErrorException
     */
    public function storeProduct(Product $p ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        try {
            $em->persist($p);
        } catch (ORMException $e ) {
            throw new CatalogErrorException($e->getMessage());
        }
    }

    /**
     * @throws CatalogErrorException
     */
    public function flush() {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        try {
            $em->flush();
        } catch (ORMException $e ) {
            throw new CatalogErrorException($e->getMessage());
        }
    }

    /**
     * @param Product $product
     * @throws CatalogDetailedException
     * @throws CatalogErrorException
     */
    public function assignAssociations(Product $product ) {
        /** @var Classificator[] $classificatorsToFind */
        $classificatorsToFind = [];

        $classificatorsToFind[] = $product->getBrand();
        $classificatorsToFind[] = $product->getLine();
        $classificatorsToFind[] = $product->getManufacturer();
        $classificatorsToFind[] = $product->getMeasure();
        $classificatorsToFind[] = $product->getPurpose();
        $classificatorsToFind[] = $product->getType();
        $classificatorsToFind[] = $product->getVendor();

        $classificatorsToFind = array_filter ( $classificatorsToFind, [$this, 'lambdaNonEmpty'] );


        $codes = array_map ( [Classificator::class, 'lambdaGetCode'], $classificatorsToFind);

        $loadedClassificators = $this->loadClassificatorsList( $codes);

        /** @var Classificator[] $loadedMap */
        $loadedMap = [];
        foreach ($loadedClassificators as $c ) {
            $loadedMap[$c->getCode()] = $c;
        }

        $messages = [];

        /** @var RelatedObjectClassificator[] $wrongObjects */
        $wrongObjects = [];
        foreach ($classificatorsToFind as $c ) {
            if ( $c==null || $c->getCode() == null ) {
                continue;
            }

            if ( !array_key_exists($c->getCode(), $loadedMap)) {
                $messages[] = 'Unfound '.$c->getCode();

                $relatedObjectClassificator = new RelatedObjectClassificator();
                $relatedObjectClassificator->classificatorCode = $c->getCode();
                $relatedObjectClassificator->correctCode = $c->getGroupCode();
                $wrongObjects[] = $relatedObjectClassificator;
                continue;
            }

            $loadedClassificator = $loadedMap[$c->getCode()];
            if ( $loadedClassificator->getGroupCode() != $c->getGroupCode() ) {
                $messages[] = 'Group for ['.$loadedClassificator->getCode().'] is wrong: ['.$loadedClassificator->getGroupCode().'] should be: ['.$c->getGroupCode().']';
                $relatedObjectClassificator = new RelatedObjectClassificator();
                $relatedObjectClassificator->classificatorCode = $c->getCode();
                $relatedObjectClassificator->correctCode = $c->getGroupCode();
                $relatedObjectClassificator->wrongCode = $loadedClassificator->getGroupCode();
                $wrongObjects[] = $relatedObjectClassificator;
                continue;
            }

            if ( count($messages) == 0 ) {
                // čia daromas priskyrimas
                $product->setClassificator($loadedClassificator);
            }
        }

        if ( count ($messages) > 0 ) {
            $detailedException = new WrongAssociationsException('Unattached classificators' );
            $detailedException->setDetails($messages );
            $detailedException->setRelatedObjects($wrongObjects);

            throw $detailedException;
        }
    }

    /**
     * @param $codes
     * @return Classificator[]
     */
    public function loadClassificatorsList($codes) {
        $class = Classificator::class;
        $dql =  /** @lang DQL */ "SELECT c,g FROM $class c join c.group g where c.code in (:codes)";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        /** @var Classificator[] $classificators */
        $classificators = $em->createQuery($dql)->setParameter('codes', $codes )->getResult();

        return $classificators;
    }

    public function lambdaNonEmpty($obj ) {
        return !empty($obj);
    }

    /**
     * @param string $code
     * @param string $groupCode
     * @param int $limit
     * @return Classificator[]
     */
    public function loadSimmilarClassificators ($code, $groupCode, $limit) {
        $subCodes = [];
        $subCodes[] = substr($code, 0, 3);
        $subCodes[] = substr($code, 0, 2);
        $subCodes[] = substr($code, 0, 1);
        $subCodes[] = '';

        /** @var Classificator[] $found */
        $found = [];
        foreach ($subCodes as $subCode ) {
            $part = $this->loadLikeClassificators($subCode, null, $groupCode, 'en', $limit );
            $found = array_merge($found, $part);
            if ( count($found ) >= $limit ) {
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
    public function loadLikeClassificators ( $likeCode, $likeName, $groupCode, $language, $limit ) {
        if ( !empty($likeName)) {
            $classificatorsLanguages = $this->loadLikeClassificatorsLanguages ($likeCode, $likeName, $groupCode, $language, $limit );
            $classificators = [];
            foreach ($classificatorsLanguages as $cl ) {
                $classificators[] =  $cl->getClassificator();
            }
            return $classificators;
        }

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $builder =  $em->createQueryBuilder();
        $builder->select('c')
            ->from(Classificator::class, 'c')
            ->join( 'c.group', 'g');


        if ( !empty($groupCode)) {
            $builder->andWhere('g.code=:groupCode');
            $builder->setParameter('groupCode', $groupCode );
        }

        if ( !empty($likeCode)) {
            $builder->andWhere('c.code like :likeCode');
            $builder->setParameter('likeCode', '%'.$likeCode.'%' );
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
    public function loadLikeClassificatorsLanguages ($likeCode, $likeName, $groupCode, $language, $limit ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $builder =  $em->createQueryBuilder();
        $builder->select('cl')
            ->from(ClassificatorLanguage::class, 'cl')
            ->join( 'cl.classificator', 'c')
            ->join( 'c.group', 'g')
            ->join( 'c.language', 'l');

        $builder->andWhere('l.code=:languageCode' );
        $builder->setParameter('languageCode', $language );

        if ( !empty($likeName) ) {
            $builder->andWhere('cl.value like :likeName');
            $builder->setParameter('likeName', '%'.$likeName.'%' );
        }

        if ( !empty($groupCode)) {
            $builder->andWhere('g.code=:groupCode');
            $builder->setParameter('groupCode', $groupCode );
        }

        if ( !empty($likeCode)) {
            $builder->andWhere('c.code like :likeCode');
            $builder->setParameter('likeCode', '%'.$likeCode.'%' );
        }

        $builder->setMaxResults($limit);

        /** @var ClassificatorLanguage[] $cls */
        $cls =  $builder->getQuery()->getResult();
        return $cls;
    }



    /**
     * @param ClassificatorLanguage[] $cls
     * @throws DBALException
     * @return int
     */
    public function importClassificatorsLangs ( $cls ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();

        $valuesLines = [];
        foreach ($cls as $cl ) {
            $line = [
                $cl->getLanguage()->getCode(),
                $cl->getClassificator()->getCode(),
                $cl->getValue()
            ];

            $qLine = array_map ( [$conn, 'quote'], $line );

            $lineStr =  '('.join (',',$qLine).')';
            $valuesLines[] = $lineStr;
        }

        $valuesStr = join (",\n", $valuesLines );

        $sql = /** @lang MySQL */ "INSERT INTO classificator_lang (language_code, classificator_code, value )
          values $valuesStr
          ON DUPLICATE  KEY UPDATE value=values(value)";

        return $conn->exec($sql);
    }

    /**
     * @param Classificator[] $cs
     * @throws DBALException
     * @return int
     */
    public function importClassificators ( $cs ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();

        $valuesLines = [];

        foreach ($cs as $c ) {
            $values = [$c->getCode(),
            $c->getGroupCode() ];

            $qValues = array_map ([$conn, 'quote'], $values);
            $line = '('. join ( ',', $qValues). ')';

            $valuesLines [] = $line;
        }

        $valuesStr = join ( ",\n", $valuesLines);

        $sql = /** @lang MySQL*/ "insert ignore into classificators ( code, group_code )
                values $valuesStr";
        return $conn->exec($sql);
    }

    /**
     * @param $sku
     * @param $languageCode
     * @return ProductLanguage
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getProductLanguage ( $sku,  $languageCode ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $class = ProductLanguage::class;

        $dql = /** @lang DQL */ "SELECT pl,p,l from $class pl join pl.product p join pl.language l where p.sku=:sku and l.code=:languageCode";
        $query = $em->createQuery($dql);

        $query->setParameter( 'sku', $sku);
        $query->setParameter('languageCode', $languageCode );

        /** @var ProductLanguage $productLanguage */
        $productLanguage = $query->getOneOrNullResult();

        return $productLanguage;
    }

    /**
     * @param ProductLanguage $pl
     * @throws CatalogErrorException
     */
    public function storeProductLanguage ( ProductLanguage $pl ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        try {
            $em->persist($pl);
        } catch (ORMException $e ) {
            throw new CatalogErrorException($e->getMessage());
        }
    }


    /**
     * @param Product[] $products
     * @return int
     * @throws DBALException
     */
    public function importProducts ( $products, $givenFieldsSet ) {
        // build insert sql script by intersecting possible fields with a given allowedFieldsSet
        $givenFields = array_keys($givenFieldsSet);
        $importingFields = array_intersect($givenFields, Product::ALLOWED_FIELDS );
        $importingFieldsAndSku = array_merge (['sku'], $importingFields );

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();

        $rows = [];
        foreach ( $products as $p  ) {
            $values = PropertiesHelper::getValuesArray($p, $importingFieldsAndSku, 'code');
            $dbValues = array_map ( [DbHelper::class, 'boolToInt'],  $values );
            $qValues = array_map([$conn, 'quote'], $dbValues);
            $row = '('. join ( ',', $qValues ) . ')';
            $rows[] = $row;
        }

        $valuesStr = join ( ",\n", $rows );
        $fieldsStr = join ( ',', $importingFieldsAndSku);

        $updates=[];
        foreach ($importingFields as $f) {
            $updateStr = "$f=VALUES($f)";
            $updates[] = $updateStr;
        }

        $updatesStr = join ( ",\n", $updates );


        $sql = /** @lang MySQL */
        "INSERT INTO products ($fieldsStr)
                VALUES $valuesStr
                ON DUPLICATE KEY UPDATE
                $updatesStr";

        $this->logger->debug('importProducts: sql='.$sql);
        return $conn->exec($sql);
    }

    /**
     * @param ProductLanguage[] $productsLangs
     * @return int
     * @throws DBALException
     */
    public function importProductsLangs( $productsLangs, $givenFieldsSet ) {
        $givenFields = array_keys($givenFieldsSet);
        $importingFields = array_intersect($givenFields, ProductLanguage::ALLOWED_FIELDS );
        $importingFieldsAndSkuLang = array_merge (['sku', 'language'], $importingFields );
        // build insert sql script by intersecting possible fields with a given allowedFieldsSet

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();

        $rows = [];
        foreach ( $productsLangs as $p  ) {
            $values = PropertiesHelper::getValuesArray($p, $importingFieldsAndSkuLang, 'code');
            $qValues = array_map([$conn, 'quote'], $values);
            $row = '('. join ( ',', $qValues ) . ')';
            $rows[] = $row;
        }

        $valuesStr = join ( ",\n", $rows );
        $fieldsStr = join ( ',', $importingFieldsAndSkuLang);

        $updates=[];
        foreach ($importingFields as $f) {
            $updateStr = "$f=VALUES($f)";
            $updates[] = $updateStr;
        }

        $updatesStr = join ( ",\n", $updates );


        $sql = /** @lang MySQL */
            "INSERT INTO products_languages ($fieldsStr)
                VALUES $valuesStr
                ON DUPLICATE KEY UPDATE
                $updatesStr";

        $this->logger->debug('importProducts: sql='.$sql);
        return $conn->exec($sql);
    }


    public function findClassificators ( $group, $codes ) {
        $class = Classificator::class;
        $dql = /** @lang DQL */ "SELECT c from  $class c join c.group g where g.code=:groupCode and c.code in (:codes)";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);

        $query->setParameter('groupCode', $group );
        $query->setParameter('codes', $codes );

        /** @var Classificator[] $classificators */
        $classificators = $query->getResult();

        return $classificators;
    }

}