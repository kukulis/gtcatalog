<?php
/**
 * ProductsBaseService.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-14
 * Time: 16:42
 */

namespace Gt\Catalog\Services;


use Gt\Catalog\Dao\CategoryDao;
use Gt\Catalog\Entity\Category;
use Gt\Catalog\Exception\CatalogValidateException;

abstract class ProductsBaseService
{
    /** @var CategoryDao */
    protected $categoryDao;

    /**
     * @param $categoriesCodes
     * @throws CatalogValidateException
     */
    protected function validateExistingCategories($categoriesCodes) {
        /** @var Category[] $categories */
        $categories = $this->categoryDao->loadCategories($categoriesCodes);

        $loadedCodes = array_map([Category::class, 'lambdaGetCode'], $categories );

        $diff = array_diff($categoriesCodes, $loadedCodes);

        if ( count($diff) > 0 ) {
            throw new CatalogValidateException('Categories codes was not found in DB :'.join ( ',', $diff));
        }
    }
}