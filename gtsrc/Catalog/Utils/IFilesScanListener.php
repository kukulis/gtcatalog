<?php
/**
 * IFilesScanListener.php
 * Created by Giedrius Tumelis.
 * Date: 2021-02-15
 * Time: 13:30
 */

namespace Gt\Catalog\Utils;


interface IFilesScanListener
{
    public function fileEncountered($filePath);
}