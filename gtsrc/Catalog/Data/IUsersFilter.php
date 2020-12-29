<?php
/**
 * IUsersFilter.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-28
 * Time: 16:29
 */

namespace Gt\Catalog\Data;


interface IUsersFilter
{
    public function getLimit();
    public function getLikeName();
}