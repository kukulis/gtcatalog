<?php
/**
 * CategoriesFilter.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-13
 * Time: 08:43
 */

namespace Gt\Catalog\Data;


use Gt\Catalog\Entity\Language;

interface CategoriesFilter
{
    /**
     * @return mixed
     */
    public function getOffset();

    public function getLimit();

    public function getLikeCode();

    public function getLikeParent();

    public function getExactParent();

    /** @return Language */
    public function getLanguage();
}