<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.28
 * Time: 20.19
 */

namespace Gt\Catalog\Exception;


class RelatedObjectClassificator implements RelatedObject
{
    public $classificatorCode;
    public $wrongCode;
    public $correctCode;

    /** @var string */
    public $suggestions=[];

}