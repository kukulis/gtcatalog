<?php
/**
 * ProductsHelper.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-14
 * Time: 11:21
 */

namespace Gt\Catalog\Utils;


class ProductsHelper
{
    public static function validateProductSku($sku) {
        if ( empty($sku)) {
            return false;
        }
        return preg_match ( '/^[a-z_0-9\\-]+$/', $sku) == 1;
    }
}