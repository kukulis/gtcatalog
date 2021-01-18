<?php
/**
 * CategoriesTree.php
 * Created by Giedrius Tumelis.
 * Date: 2021-01-18
 * Time: 12:56
 */

namespace Gt\Catalog\Utils;

use Gt\Catalog\Data\CategoryTreeItem;
use Psr\Log\LoggerInterface;

class CategoriesTree
{

    /**
     * @param CategoryTreeItem[] $treeItems
     * @return CategoryTreeItem
     */
    public static function generateTree( $treeItems, LoggerInterface  $logger) {

        $logger->debug ( 'Received '.count($treeItems). ' categories ');

        /** @var CategoryTreeItem[] $itemMap key is category_id */
        $itemMap = [];
        foreach ($treeItems as $item ) {
            $itemMap[$item->id_category] = $item;
        }

        $rootItem = new CategoryTreeItem();
        $rootItem->id_category = 'root';
        $itemMap['root'] = $rootItem;


        // assign children
        foreach ($treeItems as $item ) {
            if ( empty($item->id_parent) ) {
                $item->id_parent = 'root';
            }
            if ( !array_key_exists($item->id_parent, $itemMap)) {
                $logger->debug('Kategorijai '.$item->id_category.' nerasta tėvinė kategorija '.$item->id_parent );
                continue;
            }
            $itemMap[$item->id_parent]->children[] = $item;
        }

        self::assignLeftRight(1, $rootItem, 1 );
        $logger->debug ( 'left right assigned, writting to db' );

        return $rootItem;

    }

    /**
     * @param int $givenLeft
     * @param CategoryTreeItem $item
     * @return int
     */
    public static function assignLeftRight( $givenLeft, CategoryTreeItem & $item, $depth ) {
        $item->nleft = $givenLeft;
        $item->level_depth = $depth;
        foreach ($item->children as $child ) {
            $givenLeft++;
            $givenLeft = self::assignLeftRight($givenLeft, $child, $depth+1);
        }

        $givenLeft++;
        $item->nright = $givenLeft;
        return $givenLeft;
    }

    /**
     * @param CategoryTreeItem $node
     */
    public static function recollectItems( CategoryTreeItem $node, &$recollectedItems ) {
        $recollectedItems[] = $node;
        foreach ($node->children as $child ) {
            self::recollectItems($child, $recollectedItems);
        }
    }
}