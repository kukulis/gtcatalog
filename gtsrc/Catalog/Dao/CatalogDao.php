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

        $loadedClassificators = $this->loadList( $codes);

        /** @var Classificator[] $loadedMap */
        $loadedMap = [];
        foreach ($loadedClassificators as $c ) {
            $loadedMap[$c->getCode()] = $c;
        }

        $messages = [];
        foreach ($classificatorsToFind as $c ) {
            if ( $c==null || $c->getCode() == null ) {
                continue;
            }

            if ( !array_key_exists($c->getCode(), $loadedMap)) {
                $messages[] = 'Unfound '.$c->getCode();
                continue;
            }

            $loadedClassificator = $loadedMap[$c->getCode()];
            if ( $loadedClassificator->getGroup()->getCode() != $c->getGroup()->getCode() ) {
                $messages[] = 'Wrong group '.$loadedClassificator->getGroup()->getCode().' original: '.$c->getGroup()->getCode();
                continue;
            }

            if ( count($messages) == 0 ) {
                // čia daromas priskyrimas
                $product->setClassificator($loadedClassificator);
            }
        }

        if ( count ($messages) > 0 ) {
            $detailedException = new CatalogDetailedException('Unattached classificators' );
            $detailedException->setDetails($messages );

            throw $detailedException;
        }
    }

    /**
     * @param $codes
     * @return Classificator[]
     */
    public function loadList($codes) {
        $class = Classificator::class;
        $dql =  /** @lang DQL */ "SELECT c FROM $class c where c.code in :codes";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        /** @var Classificator[] $classificators */
        $classificators = $em->createQuery($dql)->setParameter('codes', $codes );

        return $classificators;
    }

    public function lambdaNonEmpty($obj ) {
        return !empty($obj);
    }

}