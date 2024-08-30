<?php

namespace Gt\Catalog\Utils;

use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\Product;

class ProductClassificatorAccessorMeasure implements ProductClassificatorAccessor
{
    // null is used when instance is used as factory
    // in that case classificator access function will throw null pointer exception
    private ?Product $product;
    private bool $valid = true;

    private ?Classificator $backup;

    public function __construct(?Product $product)
    {
        $this->product = $product;
    }

    public function getClassificator(): ?Classificator
    {
        return $this->product->getMeasure();
    }

    public function setClassificator(?Classificator $classificator)
    {
        $this->product->setMeasure($classificator);
    }

    // factory methods
    public function createClassificatorAccessor(Product $product) : ProductClassificatorAccessor {
        return new ProductClassificatorAccessorMeasure($product);
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }

    public function backupClassificator(): ProductClassificatorAccessor
    {
        $this->backup = $this->product->getMeasure();

        return $this;
    }

    public function restoreClassificator(): ProductClassificatorAccessor
    {
        $this->product->setMeasure($this->backup);

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function getClassificatorCode(): ?string
    {
        if( $this->product->getMeasure() != null ) {
            return $this->product->getMeasure()->getCode();
        }

        return null;
    }


}