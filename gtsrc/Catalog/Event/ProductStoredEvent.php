<?php

namespace Gt\Catalog\Event;

use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductLanguage;
use Symfony\Contracts\EventDispatcher\Event;

class ProductStoredEvent extends Event
{
    public const NAME = 'product.stored';

    private Product $product;

    private Product $oldProduct;

    private ProductLanguage $productLanguage;

    private ProductLanguage $productLanguageOld;

    private string $languageCode;

    public function __construct(
        Product $product,
        Product $oldProduct,
        ProductLanguage $productLanguage,
        ProductLanguage $productLanguageOld,
        string $languageCode)
    {
        $this->product = $product;
        $this->oldProduct = $oldProduct;
        $this->productLanguage = $productLanguage;
        $this->productLanguageOld = $productLanguageOld;
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

    public function getProductLanguage(): ProductLanguage
    {
        return $this->productLanguage;
    }

    public function getProductLanguageOld(): ProductLanguage
    {
        return $this->productLanguageOld;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }
}