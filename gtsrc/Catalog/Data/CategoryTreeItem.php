<?php
/**
 * CategoryTreeItem.php
 * Created by Giedrius Tumelis.
 * Date: 2021-01-18
 * Time: 12:56
 */

namespace Gt\Catalog\Data;


class CategoryTreeItem
{
    public $id_category;
    public $id_parent;
    public $level_depth;
    public $nleft;
    public $nright;

    public $children=[];

    public $name;
}