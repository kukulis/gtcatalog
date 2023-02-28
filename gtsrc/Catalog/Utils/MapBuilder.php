<?php

namespace Gt\Catalog\Utils;

// TODO move to a separate lib
// this might be used as lambdish/phunctional reindex

class MapBuilder
{
    public static function buildMap(array $objects, callable $keyGetter) : array {
        $map = [];
        foreach ($objects as $object) {
            $key = call_user_func($keyGetter, $object);
            $map[$key] = $object;
        }

        return $map;
    }
}