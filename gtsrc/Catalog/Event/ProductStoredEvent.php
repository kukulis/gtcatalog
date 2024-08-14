<?php

namespace Gt\Catalog\Event;

use Gt\Catalog\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductStoredEvent extends Event
{
    public const NAME = 'product.stored';

    private $product;

    private $oldProduct;

    private $productLanguage;

    public function __construct($product, $oldProduct, $productLanguage)
    {
        $this->product = $product;
        $this->oldProduct = $oldProduct;
        $this->productLanguage = $productLanguage;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getOldProduct(): Product
    {
        return $this->oldProduct;
    }

    public function getProductLanguage()
    {
        return $this->productLanguage;
    }
}