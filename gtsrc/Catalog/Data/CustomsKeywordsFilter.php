<?php
/**
 * CustomsKeywordsFilter.php
 * Created by Giedrius Tumelis.
 * Date: 2021-04-07
 * Time: 13:39
 */

namespace Gt\Catalog\Data;


interface CustomsKeywordsFilter
{
    /**
     * @return mixed
     */
    public function getOffset();

    /**
     * @return mixed
     */
    public function getLimit();
}