<?php

namespace Gt\Catalog\Utils;

use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\Product;

interface ProductClassificatorAccessor
{
    public function getClassificator(): Classificator;

    public function setClassificator(Classificator $classificator);

    public function createClassificatorAccessor(Product $product): ProductClassificatorAccessor;

    public function backupClassificator(): ProductClassificatorAccessor;

    public function restoreClassificator(): ProductClassificatorAccessor;

    public function isValid(): bool;

    public function setValid(bool $valid): void;

    public function getProduct(): ?Product;
}