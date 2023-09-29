<?php

namespace Gt\Catalog\Transformer;

use Catalog\B2b\Common\Data\Catalog\Category as CatalogCategory;
use Gt\Catalog\Entity\CategoryLanguage;

class CategoryTransformer
{
    public static function transformToRest(CategoryLanguage $cl): CatalogCategory
    {
        $category = new CatalogCategory();
        $category->code = $cl->getCode();
        $category->name = $cl->getName();
        $category->description = $cl->getDescription();
        $category->language = $cl->getLanguageCode();
        $category->parent = $cl->getCategory()->getParentCode();
        $category->customsCode = $cl->getCategory()->getCustomsCode();
        $category->dateCreated = $cl->getCategory()->getDateCreated();
        $category->confirmed = $cl->getCategory()->isConfirmed();

        return $category;
    }
}