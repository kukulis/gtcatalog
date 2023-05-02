<?php

namespace Gt\Catalog\Exception;

class RelatedObjectClassificator implements RelatedObject
{
    public $classificatorCode;
    public $wrongCode;
    public $correctCode;

    /** @var string */
    public $suggestions=[];

}