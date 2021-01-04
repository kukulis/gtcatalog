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
    /**
     * @param string $name
     * @return string
     */
    public static function canonizePictureName ( $name ) {
        $rez = preg_replace( '/[^[:alnum:].]+/', '_', $name);
        return $rez;
    }

    /**
     * @param string $base
     * @param int $id
     * @param string $name
     * @param string $pathSeparator
     * @return string
     */
    public static function calculateImagePath( $base, $id, $pathSeparator='/') {
        $pathElems=[
            $base,
        ];
        $idStr = "".$id;
        for ($i=0; $i<strlen($idStr); $i++ ) {
            $char = $idStr[$i];
            $pathElems[] = $char;
        }
        return join($pathSeparator, $pathElems);
    }

    public static function prefixWithSlash ($str) {
        if ( $str == null ) {
            return $str;
        }
        if (!(strpos($str, '/') === 0) ) {
            return  '/'.$str;
        }
        return $str;
    }
}