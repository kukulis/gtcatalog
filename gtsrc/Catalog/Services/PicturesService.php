<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.9.25
 * Time: 12.57
 */

namespace Gt\Catalog\Services;


use Gt\Catalog\Entity\Picture;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Utils\PicturesHelper;

class PicturesService
{
    /** @var PicturesDao */
    private $picturesDao;

    public function createPicture($path, Product $product) {

        // 1) sukurti pav objektą

        $fileName = basename($path);
        $canonizedName = PicturesHelper::canonizePictureName($fileName);
        $picture = new Picture();
        $picture->setName($canonizedName);



        // 2) paskaičiuoti jam kelią
        // 3) nukopijuoti paduotą pav į reikiamą vietą
    }

}