<?php
/**
 * CategoriesHelper.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-14
 * Time: 11:20
 */

namespace Gt\Catalog\Utils;


class CategoriesHelper
{
    public static function splitCategoriesStr($str) {
        if ( $str == null ) {
            return [];
        }
        $rez = preg_split ( '/[\\s]+/', $str, -1, PREG_SPLIT_NO_EMPTY );
        return $rez;
    }

    public static function validateCategoryCode($code) {
        if ( empty($code)) {
            return false;
        }
        return preg_match ( '/^[a-z_0-9\\-]+$/', $code) == 1;
    }

    public static function validateClassificatorCode($code) {
        return self::validateCategoryCode($code);
    }
}