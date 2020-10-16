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
     * @param string $path
     * @param string $name
     * @return Picture
     */
    public function createPicture($path, $name) {
        // 1) sukurti pav objektą
        $canonizedName = PicturesHelper::canonizePictureName($name);
        $picture = new Picture();
        $picture->setName($canonizedName);
        $picture = $this->picturesDao->insertPicture($picture);

        // 2) paskaičiuoti jam kelią
        $targetDir = PicturesHelper::calculateImagePath( $this->baseDir, $picture->getId(), $this->pathSeparator);
        $fullDir = $this->rootPath. $this->pathSeparator .$targetDir;

        if (!file_exists($fullDir)) {
            @mkdir($fullDir, 0775, true);
        }

        $fullPath = $fullDir.$this->pathSeparator.$picture->getName();

        // 3) nukopijuoti paduotą pav į reikiamą vietą
        copy ( $path, $fullPath );

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
     * @param Product $product
     * @param Picture $picture
     */
    public function assignPictureToProduct ( Product $product, Picture $picture ) {
        $productPicture = new ProductPicture();
        $productPicture->setProduct($product);
        $productPicture->setPicture($picture);

        $this->picturesDao->assignProductPicture($productPicture);
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
}