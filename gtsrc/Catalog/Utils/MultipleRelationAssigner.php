<?php

namespace Gt\Catalog\Utils;

// TODO move to a separate lib

class MultipleRelationAssigner
{
    /**
     * @param array $parents Parent object.
     * @param array $children Children objects.
     * @param callable $parentKeyGetter unique key in parents array
     * @param callable $childKeyGetter non unique key in children array
     * @param callable $assigner function to assign child to parent
     */
    public static function assignByRelation(
        array $parents,
        array $children,
        callable $parentKeyGetter,
        callable $childKeyGetter,
        callable $assigner
    ) {
        $map = MapBuilder::buildMap($parents, $parentKeyGetter);

        foreach ($children as $child) {
            $key = call_user_func($childKeyGetter, $child);

            if ( !array_key_exists($key, $map)) {
                continue;
            }
            $parent = $map[$key];
            call_user_func($assigner, $parent, $child);
        }
    }
}