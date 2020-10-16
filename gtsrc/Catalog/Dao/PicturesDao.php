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
        $dql = /** @lang DQL */ "SELECT pp, pi from $class pp join pp.product pr join pp.picture pi where pr.sku = :sku";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
        $query->setParameter('sku', $sku );

        /** @var ProductPicture[] $productPictures */
        $productPictures = $query->getResult();

        return $productPictures;
    }

    /**
     * @param string[] $skus
     */
    public function getProductsPictures( $skus ) {
        $class = ProductPicture::class;
        $dql = /** @lang DQL */ "SELECT pp, pi from $class pp join pp.product pr join pp.picture pi where pr.sku in (:skus)";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
        $query->setParameter('skus', $skus );

        /** @var ProductPicture[] $productPictures */
        $productPictures = $query->getResult();

        return $productPictures;
    }

    /**
     * @param string $sku
     * @param int $id
     * @return ProductPicture
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findPictureAssignement($sku, $id) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $class = ProductPicture::class;
        $dql = /** @lang DQL */"SELECT pp from $class pp join pp.product pr join pp.picture pi where pr.sku=:sku and pi.id=:id";
        $query =$em->createQuery($dql);
        $query->setParameter('sku', $sku );
        $query->setParameter('id', $id );

        /** @var ProductPicture $pp */
        $pp = $query->getSingleResult();

        return $pp;
    }

    /**
     * @param ProductPicture $productPicture
     */
    public function deletePictureAssignement ( ProductPicture $productPicture ) {
        $em = $this->doctrine->getManager();
        $em->remove($productPicture);
        $em->flush();
    }
}