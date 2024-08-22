<?php

namespace Gt\Catalog\Event;

use Gt\Catalog\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductRemoveEvent extends Event
{
    public const NAME = 'product.remove';

    private Product $product;
    private $languageCode;

    public function __construct(Product $product, $languageCode)
    {
        $this->product = $product;
        $this->languageCode = $languageCode;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return mixed
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }
}