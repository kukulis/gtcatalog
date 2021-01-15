<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.9.25
 * Time: 12.57
 */

namespace Gt\Catalog\Services;


use Doctrine\ORM\ORMException;
use Gt\Catalog\Dao\PicturesDao;
use Gt\Catalog\Entity\Picture;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductPicture;
use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Utils\PicturesHelper;

class PicturesService
{
    /** @var PicturesDao */
    private $picturesDao;

    /** @var string */
    private $rootPath;

    /** @var string */
    private $baseDir;

    /** @var string */
    private $pathSeparator;

    /**
     * PicturesService constructor.
     * @param PicturesDao $picturesDao
     */
    public function __construct(PicturesDao $picturesDao)
    {
        $this->picturesDao = $picturesDao;
        $this->pathSeparator = DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $sourcePath
     * @param string $name
     * @return Picture
     * @throws CatalogValidateException
     */
    public function createPicture($sourcePath, $name, $checkHash=false, $statusas=null, $infoProvider=null) {
        // 1) sukurti pav objektą
        $canonizedName = PicturesHelper::canonizePictureName($name);
        $picture = new Picture();
        $picture->setName($canonizedName);
        $picture->setStatusas($statusas);
        $picture->setInfoProvider($infoProvider);
        $picture = $this->picturesDao->insertPicture($picture);

        // 2) paskaičiuoti jam kelią
        $targetDir = PicturesHelper::calculateImagePath( $this->baseDir, $picture->getId(), $this->pathSeparator);
        $fullDir = $this->rootPath. $this->pathSeparator .$targetDir;

        if (!file_exists($fullDir)) {
            @mkdir($fullDir, 0775, true);
        }

        $destinationFullPath = $fullDir.$this->pathSeparator.$picture->getName();

        // calculate picture hash before inserting into database
        $content = file_get_contents($sourcePath);
        $hash = hash("md5", $content);

        if ( $checkHash) {
            $duplicatePicture = $this->picturesDao->findByHash($hash);

            if ( $duplicatePicture != null ) {
                // find pictures products
                $productPicture = $this->picturesDao->findPictureProduct ( $duplicatePicture->getId() );

                $productInfo = '';
                if ( $productPicture != null ) {
                    $productInfo='  assigned to product '.$productPicture->getProduct()->getSku();
                }

                throw new CatalogValidateException('ERROR: For picture '. $name .' we found a duplicate picture with id '.$duplicatePicture->getId().$productInfo);
            }
        }

        $picture->setContentHash($hash);
        $this->picturesDao->updatePicture($picture);

        // 3) nukopijuoti paduotą pav į reikiamą vietą
        file_put_contents($destinationFullPath, $content );

        return $picture;
    }

    /**
     * @param $id
     * @param $name
     * @return string
     */
    public function calculatePicturePath($id, $name, $separator=null ) {
        if ( $separator == null ) {
            $separator = $this->pathSeparator;
        }
        $targetDir = PicturesHelper::calculateImagePath( $this->baseDir, $id, $separator);
        return $targetDir.$this->pathSeparator.$name;
    }

    /**
     * @param $id
     * @param null|string $separator
     * @return string
     */
    public function calculatePictureFullDir($id, $separator=null) {
        if ( $separator == null ) {
            $separator = $this->pathSeparator;
        }
        $targetDir = PicturesHelper::calculateImagePath( $this->baseDir, $id, $this->pathSeparator);
        $fullDir = $this->rootPath. $separator .$targetDir;
        return $fullDir;
    }

    /**
     * @param $id int
     * @param $name string
     * @param string|null $separator
     * @return string
     */
    public function calculatePictureFullPath($id, $name, $separator=null) {
        if ( $separator == null ) {
            $separator = $this->pathSeparator;
        }
        $fullDir = $this->calculatePictureFullDir($id, $separator);
        $fullPath = $fullDir.$separator.$name;
        return $fullPath;
    }

    /**
     * @param Product $product
     * @param Picture $picture
     * @return ProductPicture
     */
    public function assignPictureToProduct ( Product $product, Picture $picture, $priority = 1 ) {
        $productPicture = new ProductPicture();
        $productPicture->setProduct($product);
        $productPicture->setPicture($picture);
        $productPicture->setPriority(intval($priority));

        $this->picturesDao->assignProductPicture($productPicture);
        return $productPicture;
    }


    /**
     * @param string $sku
     * @return ProductPicture[]
     */
    public function getProductPictures ( $sku ) {
        $productPictures = $this->picturesDao->getProductPictures($sku);
        foreach ($productPictures as $pp ) {
            $picture = $pp->getPicture();
            $path = '/'. $this->calculatePicturePath($picture->getId(), $picture->getName(), '/');
            $picture->setConfiguredPath($path);
        }
        return $productPictures;
    }

    // =====================================================================
    // ========================= DI ========================================
    // =====================================================================

    /**
     * @param string $rootPath
     */
    public function setRootPath(string $rootPath): void
    {
        $this->rootPath = $rootPath;
    }

    /**
     * @param string $baseDir
     */
    public function setBaseDir(string $baseDir): void
    {
        $this->baseDir = $baseDir;
    }

    /**
     * @param string $sku
     * @param int $id
     * @return bool
     * @throws CatalogErrorException
     */
    public function unassignPicture ( $sku, $id ) {
        // pašalinam tik prisegimą prie produkto.
        // paveikslėlius pačius trinsime atskiru procesu.
        // Toks užmanymas dėl to, kad kai bus versijuojami produktai,
        // gali būti, kad reiks atstatyti pav. ištrynimą. Tokiu būdu pav. turės būti failinėj sistemoj.
        // Su tuo atskiru procesu trinsime senus nepririštus prie produktų, paveikslėius.

        try {
            $pp = $this->picturesDao->findPictureAssignement($sku, $id);
            if ($pp == null) {
                throw new CatalogErrorException('The picture ' . $id . ' is not assigned to product ' . $sku);
            }

            $this->picturesDao->deletePictureAssignement($pp);
            return true;
        } catch (ORMException $e ) {
            throw new CatalogErrorException( $e->getMessage());
        }
    }

    /**
     * @param string $sku
     * @param int $id_picture
     * @return ProductPicture
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getProductPicture ($sku, $id_picture) {
        $pp = $this->picturesDao->findPictureAssignement($sku, $id_picture);
        return $pp;
    }

    /**
     * @param ProductPicture $productPicture
     */
    public function storeProductPictureWithPicture (ProductPicture $productPicture) {
        $this->picturesDao->storeProductPicture($productPicture);
    }

    public function unassignPictureByPriority($sku, $priority ) {
        $pp = $this->picturesDao->findPictureAssignmentByPriority($sku, $priority);
        $this->picturesDao->deletePictureAssignement($pp);
    }

}