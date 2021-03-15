<?php
/**
 * IBrandsFilter.php
 * Created by Giedrius Tumelis.
 * Date: 2021-03-15
 * Time: 13:17
 */

namespace Gt\Catalog\Data;


interface IBrandsFilter
{
    /**
     * @return mixed
     */
    public function getOffset();

    /**
     * @return mixed
     */
    public function getLimit();

    /**
     * @return mixed
     */
    public function getLikeName();

    public function getStartsLike();
}