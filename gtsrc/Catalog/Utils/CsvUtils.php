<?php
/**
 * CsvUtils.php
 * Created by Giedrius Tumelis.
 * Date: 2020-09-18
 * Time: 16:22
 */

namespace Gt\Catalog\Utils;


class CsvUtils
{
    public static function arrayToAssoc ( $headMap, $line ) {
        $mapLine = [];
        foreach ($headMap as $key => $index) {
            $mapLine[$key] = $line[$index];
        }
        return $mapLine;
    }
}