<?php
/**
 * BaseDao.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-14
 * Time: 09:22
 */

namespace Gt\Catalog\Dao;


use Doctrine\DBAL\Connection;
use Gt\Catalog\Utils\PropertiesHelper;
use \Closure;

class BaseDao
{
    /**
     * @param Connection $con
     * @return \Closure
     */
    public static function getQuoter ( Connection $con ) {
        $f = function ($str) use($con) {
            if ( $str === null ) {
                return 'null';
            }
            else if ( $str === false ) {
                return 0;
            }
            else if ( $str === true ) {
                return 1;
            }
            else {
                return $con->quote($str);
            }
        };
        return $f;
    }

    /**
     * @param $dataArray
     * @param $fields
     * @param $updatedFields
     * @param \Closure $quoter
     * @param $subobjectProperty
     * @return string
     */
    public function buildImportSql( $dataArray, $fields, $updatedFields, Closure $quoter, $subobjectProperty, $tableName) {
        $rows = [];
        foreach ($dataArray as $dataObj ) {
            $values = PropertiesHelper::getValuesArray($dataObj, $fields, $subobjectProperty);
            $qValues = array_map($quoter, $values);
            $row = '('. join ( ',', $qValues ) . ')';
            $rows[] = $row;
        }

        $valuesStr = join ( ",\n", $rows );

        $escapedFields = array_map([self::class, 'escapeField'], $fields);
        $fieldsStr = join ( ',', $escapedFields);

        $updates=[];
        foreach ($updatedFields as $f) {
            $updateStr = "$f=VALUES($f)";
            $updates[] = $updateStr;
        }

        $updatesInstruction = '';
        $ignoreInstruction = '';

        if( count($updates) > 0 ) {
            $updatesStr = join(",\n", $updates);

            $updatesInstruction = "ON DUPLICATE KEY UPDATE
                $updatesStr";
        }
        else {
            $ignoreInstruction = 'IGNORE';
        }

        $sql = /** @lang MySQL */
            "INSERT $ignoreInstruction  INTO $tableName ($fieldsStr)
                VALUES $valuesStr
                $updatesInstruction";

        return $sql;
    }

    public static function escapeField ($fieldName ) {
        return '`'.$fieldName.'`';
    }
}