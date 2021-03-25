<?php
/**
 * PicturesDao.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-12
 * Time: 10:38
 */

namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Entity\Picture;
use Gt\Catalog\Entity\ProductPicture;
use Psr\Log\LoggerInterface;

class PicturesDao
{
    const COL_SKU = 'sku';

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
     * @return ProductPicture[]
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
     * @param string[] $skus
     * @param int $step
     * @return ProductPicture[]
     */
    public function batchGetProductsPictures( $skus, $step ) {
        /** @var ProductPicture[] $productPicturesTotal */
        $productPicturesTotal = [];

        for ( $i = 0; $i < count($skus); $i+= $step ) {
            $part = array_slice ( $skus, $i, $step);
            $productPictures = $this->getProductsPictures($part);
            $productPicturesTotal = array_merge($productPicturesTotal, $productPictures);
        }
        return $productPicturesTotal;
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
    public function deletePictureAssignement ( ProductPicture $productPicture, $flush = true ) {
        $em = $this->doctrine->getManager();
        $em->remove($productPicture);
        if ( $flush ) {
            $em->flush();
        }
    }

    public function storeProductPicture(ProductPicture $productPicture) {
        $em = $this->doctrine->getManager();
        $em->persist($productPicture);
        $em->flush();
    }

    public function updatePicture(Picture $picture) {
        $em = $this->doctrine->getManager();
        $em->persist($picture);
        $em->flush();
    }


    /**
     * @param string $hash
     * @return Picture|null
     */
    public function findByHash ( $hash ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $class = Picture::class;

        $dql = "SELECT p FROM $class p WHERE p.contentHash=:contentHash";
        $query = $em->createQuery($dql);
        $query->setParameter('contentHash', $hash);

        /** @var Picture[] $pictures */
        $pictures = $query->getResult();

        if ( count($pictures) == 0 ) {
            return null;
        }
        return $pictures[0];
    }

    public function findPictureProduct ( $pictureID ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $class = ProductPicture::class;

        $dql = "SELECT pp FROM $class pp JOIN pp.picture p WHERE p.id=:pictureID";
        $query = $em->createQuery($dql);
        $query->setParameter('pictureID', $pictureID);

        /** @var ProductPicture[] $pps */
        $pps = $query->getResult();

        if ( count($pps) == 0 ) {
            return null;
        }

        return $pps[0];
    }

    /**
     * @param string $sku
     * @param int $priority
     * @return ProductPicture|null
     */
    public function findPictureAssignmentByPriority($sku, $priority) {
        $class = ProductPicture::class;
        $dql = /** @lang DQL */ "SELECT pp FROM $class pp JOIN pp.product p  WHERE p.sku=:sku and pp.priority=:priority";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $query = $em->createQuery($dql);
        $query->setParameter('sku', $sku );
        $query->setParameter('priority', $priority );

        /** @var ProductPicture[] $pps */
        $pps = $query->getResult();

        if ( count($pps) == 0 ) {
            return null;
        }

        return $pps[0];
    }

    /**
     * @return int[]
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllPicturesIds () {
        $idsSql = /** @lang MySQL */ "SELECT id from pictures";
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();

        /** @var int[] $ids */
        $ids = $conn->executeQuery($idsSql)->fetchAll(FetchMode::COLUMN);
        return $ids;
    }

    /**
     * @param int [] $ids
     * @return Picture[]
     */
    public function getPictures ( $ids ) {
        $class = Picture::class;
        $dql = /** @lang DQL */ "SELECT p FROM $class p WHERE p.id in (:ids)";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql)->setParameter('ids', $ids );

        /** @var Picture[] $pictures */
        $pictures = $query->getResult();

        return $pictures;
    }

    public function deletePicture ( Picture  $p, $doFlush ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $em->remove($p);

        if ( $doFlush ) {
            $em->flush();
        }
    }

    public function flush() {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $em->flush();
    }

    /**
     * @param $pictureId
     * @return ProductPicture[]
     */
    public function findPictureAssignementsById($pictureId) {
        $class = ProductPicture::class;
        $dql = /** @lang DQL */  "SELECT pp FROM $class pp JOIN pp.picture pic WHERE pic.id=:pictureId";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
        $query->setParameter ('pictureId', $pictureId);

        /** @var ProductPicture[] $pps */
        $pps = $query->getResult();

        return $pps;
    }

    /**
     * @param int $fromId
     * @param int $limit
     * @return Picture[]
     */
    public function getSomePictures ( $fromId, $limit ) {
        $class = Picture::class;
        $dql = /** @lang DQL */ "SELECT pic FROM $class pic WHERE pic.id > :fromId ORDER BY pic.id";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
        $query->setParameter('fromId', $fromId );
        $query->setMaxResults($limit);

        /** @var Picture[] $pictures */
        $pictures = $query->getResult();

        return $pictures;
    }

    /**
     * @param string[] $skus
     * @return ProductPicture[]
     */
    public function loadProductsPicturesBySkus($skus)
    {
        $ppiClass = ProductPicture::class;
        $dql = /** @lang DQL */
        "SELECT ppi, pi FROM $ppiClass ppi JOIN ppi.picture as pi JOIN ppi.product as p 
         WHERE p.sku in (:skus)";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $query = $em->createQuery($dql);
        $query->setParameter('skus', $skus );

        /** @var ProductPicture[] $pps */
        $pps = $query->getResult();

        return $pps;
    }


    /**
     * @param $header
     * @param array $linesMapsMap
     * @param ProductPicture[] $productsPictures
     * @return int
     * @throws DBALException
     */
    public function upsertPicturesMetas( $header, $linesMapsMap, $productsPictures) {
        if ( count($productsPictures) == 0 ) {
            return 0;
        }
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();

        // ========= values ===========
        $sqlLines = [];
        foreach ($productsPictures as $ppi ) {
            $sku = $ppi->getProduct()->getSku();
            $pictureId = $ppi->getPicture()->getId();
            $lineMap = $linesMapsMap[$sku];

            $sqlLine=[
                $pictureId,
            ];

            // extract values, depending on header
            foreach ($header as $column) {
                if ( $column == self::COL_SKU ) {
                    // SKIP sku, as it doesnt belong to picture object
                    continue;
                }
                $value = $lineMap[$column];
                $sqlLine[] = $conn->quote($value);
            }
            $sqlLines[] = '('. join (',', $sqlLine ) . ')';
        }

        $valuesStr = join ( ",\n", $sqlLines );

        // ============== columns =======================

        $columnsNames = ['id'];
        foreach ($header as $column ) {
            if ( $column == self::COL_SKU) {
                continue;
            }
            $columnsNames[] = $column;
        }
        $columnsNamesStr = join (',', $columnsNames );



        // =========== updates ================================
        $updatesLines = [];
        // updates statements by header
        foreach ($header as $column ) {
            if ( $column == self::COL_SKU) {
                continue;
            }
            $updatesLines[] = "$column=values($column)";
        }

        $updatesStr='';
        if ( count($updatesLines )) {
            $updatesLinesStr = join ( ',', $updatesLines );
            $updatesStr = 'ON DUPLICATE KEY UPDATE '.$updatesLinesStr;
        }

        // ============= the SQL ==============================
        $sql =
            "INSERT INTO pictures($columnsNamesStr)
             VALUES $valuesStr
             ON DUPLICATE KEY UPDATE $updatesStr";

        // ============== executing sql ========================
        return $conn->exec($sql);
    }
}