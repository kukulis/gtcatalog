<?php
/**
 * CategoriesMapper.php
 * Created by Giedrius Tumelis.
 * Date: 2021-03-31
 * Time: 10:16
 */

namespace Gt\Catalog\Utils;


use Catalog\B2b\Common\Data\Catalog\Category as RestCategory;
use Gt\Catalog\Entity\Category;
use Gt\Catalog\Entity\CategoryLanguage;

class CategoriesMapper
{
    public static function mapCategoryLanguageToRestCategory(CategoryLanguage $categoryLanguage) {
        $restCategory = new RestCategory();
        $restCategory->language=$categoryLanguage->getLanguage()->getCode();
        $restCategory->name = $categoryLanguage->getName();
        $restCategory->description = $categoryLanguage->getDescription();

        $restCategory->code = $categoryLanguage->getCategory()->getCode();
        $restCategory->parent = $categoryLanguage->getCategory()->getParentCode();
        $restCategory->customsCode = $categoryLanguage->getCategory()->getCustomsCode();
        $restCategory->confirmed = $categoryLanguage->getCategory()->isConfirmed();
        $restCategory->dateCreated = $categoryLanguage->getCategory()->getDateCreated();
        return $restCategory;
    }

    public static function mapCategoryToRestCategory (Category $category) {
        $restCategory = new RestCategory();
        $restCategory->code = $category->getCode();
        $restCategory->parent = $category->getParentCode();
        $restCategory->customsCode = $category->getCustomsCode();
        $restCategory->confirmed = $category->isConfirmed();
        $restCategory->dateCreated = $category->getDateCreated();
        return $restCategory;
    }

}