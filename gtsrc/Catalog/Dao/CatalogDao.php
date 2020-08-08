<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 13.32
 */

namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Exception\CatalogDetailedException;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\WrongAssociationsException;
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
        $wrongObjects = [];
        foreach ($classificatorsToFind as $c ) {
            if ( $c==null || $c->getCode() == null ) {
                continue;
            }

            if ( !array_key_exists($c->getCode(), $loadedMap)) {
                $messages[] = 'Unfound '.$c->getCode();

                $wrongObjects[] = $c;
                continue;
            }

            $loadedClassificator = $loadedMap[$c->getCode()];
            if ( $loadedClassificator->getGroupCode() != $c->getGroupCode() ) {
                $messages[] = 'Wrong group ['.$loadedClassificator->getGroupCode().'] original: ['.$c->getGroupCode().']';
                $wrongObjects[] = $c;
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

        /** @var Classificator[] $found */
        $found = [];
        foreach ($subCodes as $subCode ) {
            $part = $this->loadLikeClassificators($subCode, null, $groupCode, $limit );
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
    public function loadLikeClassificators ( $likeCode, $likeName, $groupCode, $limit ) {
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
            $builder->andWhere('c.code = :likeCode');
            $builder->setParameter('likeCode', $likeCode );
        }

        if ( !empty($likeName) ) {
            $builder->andWhere('c.name = :likeName');
            $builder->setParameter('likeName', $likeName );
        }

        $builder->setMaxResults($limit);

        /** @var Classificator[] $classificators */
        $classificators = $builder->getQuery()->getResult();

        return $classificators;
    }


}