<?php

namespace Gt\Catalog\Event;

use Gt\Catalog\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductStoredEvent extends Event
{
    public const NAME = 'product.stored';

    private $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}