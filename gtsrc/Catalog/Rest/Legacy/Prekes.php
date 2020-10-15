<?php
/**
 * Prekes.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-15
 * Time: 15:22
 */

namespace Gt\Catalog\Rest\Legacy;


class Prekes
{
    public $resultCode;
    public $errorMessage;
    public $message;
    public $dataType;
    public $data;
    public $hintName;

    /** @var KatalogasPreke[] */
    public $PrekesList=[];
}