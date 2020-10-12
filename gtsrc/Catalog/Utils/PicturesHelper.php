<?php
/**
 * PicturesHelper.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-12
 * Time: 10:10
 */

namespace Gt\Catalog\Utils;


class PicturesHelper
{
    public static function canonizePictureName ( $name ) {
        $rez = preg_replace( '/[^[:alnum:].]+/', '_', $name);
        return $rez;
    }
}