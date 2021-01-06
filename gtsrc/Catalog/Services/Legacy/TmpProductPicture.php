<?php
/**
 * TmpProductsPictures.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-23
 * Time: 14:32
 */

namespace Gt\Catalog\Services\Legacy;


class TmpProductPicture
{
    public $priority;   // priority ??
    public $sku;
    public $picture_id;
    public $legacy_id; // key, reference
    public $url;
    public $name;
    public $is_downloaded;
    public $info_provider;
    public $statusas;
}