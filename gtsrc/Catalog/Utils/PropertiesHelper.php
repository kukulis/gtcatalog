<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.9.21
 * Time: 13.21
 */

namespace Gt\Catalog\Utils;

use \DateTime;

class PropertiesHelper
{
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param $propertyName
     * @param $objs
     * @return array
     */
    public static function getProperties( $propertyName, $objs, $subobjectProperty=null ) {
        $getter = 'get'. self::removeUnderScores($propertyName);
        $props = [];
        foreach ($objs as $o ) {
            $val = $o->$getter();
            if ( is_object($val) and $subobjectProperty != null ) {
                $subvalGetter = 'get'.$subobjectProperty;
                $subVal = $val->$subvalGetter();
                $val = $subVal;
            }

            if ( !empty($val)) {
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

            $getter = 'get'. self::removeUnderScores($p);
            $val = $obj->$getter();

            if ( is_object($val) && get_class($val) == DateTime::class ) {
                    /** @var \DateTime $dtVal */
                $dtVal = $val;
                $values[] = $dtVal->format(self::DATE_TIME_FORMAT );
            }
            elseif ( is_object($val)  and $subobjectProperty != null  ) {
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

    public static function removeUnderScores($prop) {
        return str_replace( '_', '', $prop );
    }

    /**
     * @param mixed $obj
     * @param string $property
     * @param string $subproperty
     * @return mixed
     */
    public static function getSubProperty ( $obj, $property, $subproperty ) {
        $subObject = $obj->{'get'.$property}();
        if ( $subObject == null ) {
            return null;
        }
        return $subObject->{'get'.$subproperty}();
    }


    /**
     * @param mixed $obj
     * @param string $property
     * @return mixed
     */
    public static function getPropertyOrNull ( $obj = null, $property='' ) {
        if ( $obj == null ) {
            return null;
        }
        return $obj->{'get'.$property}();
    }

    /**
     * @param array $map
     * @param string $key
     * @param string $property
     * @return mixed
     */
    public static function getPropertyFromMap ( $map, $key, $property ) {
        if ( array_key_exists($key, $map )) {
            return $map[$key]->{'get'.$property}();
        }
        return null;
    }
}