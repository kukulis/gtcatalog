<?php
/**
 * IPicturesJobsFilter.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-30
 * Time: 15:29
 */

namespace Gt\Catalog\Data;


interface IPicturesJobsFilter
{
    /**
     * @return mixed
     */
    public function getLimit();

    /**
     * @return mixed
     */
    public function getStatus();
}