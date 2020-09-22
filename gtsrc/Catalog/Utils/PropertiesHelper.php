<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.9.21
 * Time: 13.21
 */

namespace Gt\Catalog\Utils;


class PropertiesHelper
{
    /**
     * @param $propertyName
     * @param $objs
     * @return array
     */
    public static function getProperties( $propertyName, $objs, $subobjectProperty=null ) {
        $getter = 'get'.$propertyName;
        $props = [];
        foreach ($objs as $o ) {
            $val = $o->$getter();
            if ( is_object($val) and $subobjectProperty != null ) {
                $subvalGetter = 'get'.$subobjectProperty;
                $subVal = $val->$subvalGetter();
                $props[] = $subVal;
            }
            else {
                $props[] = $val;
            }
        }
        return $props;
    }


    /**
     * @param $propertyName
     * @param $objs
     * @return array
     */
    public static function buildMap ( $propertyName, $objs ) {
        $getter = 'get'.$propertyName;

        $map = [];
        foreach ($objs as $o ) {
            $p = $o->$getter();
            $map[$p] = $o;
        }
        return $map;
    }

    /**
     * @param $obj
     * @param $properties
     * @return array
     */
    public static function getValuesArray ( $obj, $properties, $subobjectProperty=null ) {
        $values = [];
        foreach ($properties as $p ) {
            $getter = 'get'.$p;
            $val = $obj->$getter();

            if ( is_object($val) and $subobjectProperty != null ) {
                $subvalGetter = 'get'.$subobjectProperty;
                $subVal = $val->$subvalGetter();
                $values[] = $subVal;
            }
            else {
                $values[] = $val;
            }

        }
        return $values;
    }

}