<?php
/**
 * PicturesDao.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-12
 * Time: 10:38
 */

namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Entity\Picture;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductPicture;
use Psr\Log\LoggerInterface;

class PicturesDao
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Registry */
    private $doctrine;

    /**
     * PicturesDao constructor.
     * @param LoggerInterface $logger
     * @param Registry $doctrine
     */
    public function __construct(LoggerInterface $logger, Registry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    /**
     * @param Picture $p
     * @return Picture
     */
    public function insertPicture (Picture $p) {
        $em = $this->doctrine->getManager();
        $em->persist($p);
        $em->flush();
        return $p;
    }

    /**
     * @param ProductPicture $productPicture
     */
    public function assignProductPicture ( ProductPicture $productPicture ) {
        $em = $this->doctrine->getManager();
        $em->persist($productPicture);
        $em->flush();
    }

    /**
     * @param string $sku
     * @return ProductPicture[]
     */
    public function getProductPictures($sku) {
        $class = ProductPicture::class;
        $dql = /** @lang DQL */ "SELECT pp, pi from $class join pp.product pr join pp.picture pi where pr.sku = :sku";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
        $query->setParameter('sku', $sku );

        /** @var ProductPicture[] $productPictures */
        $productPictures = $query->getResult();

        return $productPictures;
    }
}