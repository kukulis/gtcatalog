<?php

namespace Gt\Catalog\Event;

use Gt\Catalog\Entity\Product;
use Symfony\Contracts\EventDispatcher\Event;

class ProductStoredEvent extends Event
{
    public const NAME = 'product.stored';

    private Product $product;

    private Product $oldProduct;

    private string $languageCode;

    // TODO (FF) yra dar prekės kalbos laukai saugomi atskirame objekte ProductLanguage reikia ir šito objekto istorijos.
    public function __construct(Product $product, Product $oldProduct, string $languageCode)
    {
        $this->product = $product;
        $this->oldProduct = $oldProduct;
        $this->languageCode = $languageCode;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getOldProduct(): Product
    {
        return $this->oldProduct;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }
}