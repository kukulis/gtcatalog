<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.9.25
 * Time: 12.57
 */

namespace Gt\Catalog\Services;


use Gt\Catalog\Dao\PicturesDao;
use Gt\Catalog\Entity\Picture;
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

    public function assignPictureToProduct ( $sku, $picture ) {
        // TODO
    }

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

}