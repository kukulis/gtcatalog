<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.9.23
 * Time: 02.51
 */

namespace Gt\Catalog\Utils;


class DbHelper
{
    public static function boolToInt ($val) {
        if ( is_bool($val)) {
            if ( $val ) {
                return 1;
            }
            else {
                return 0;
            }
        }
        else {
            return $val;
        }
    }
}