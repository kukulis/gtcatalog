<?php
/**
 * ProductsHelper.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-14
 * Time: 11:21
 */

namespace Gt\Catalog\Utils;


use Gt\Catalog\Entity\Product;

class ProductsHelper
{
    public static function validateProductSku($sku) {
        if ( empty($sku)) {
            return false;
        }
        return preg_match ( '/^[a-z_0-9\\-]+$/', $sku) == 1;
    }

    /**
     * @param Product[] $products
     * @return string[]
     */
    public static function getAllClassificatorsCodes ( $products ) {
        $allCodes = [];
        foreach (Product::CLASSIFICATORS_GROUPS as $cg ) {
            $propeties = PropertiesHelper::getProperties($cg, $products, 'code');
            $allCodes = array_merge($allCodes, $propeties);
        }
        return $allCodes;
    }

}