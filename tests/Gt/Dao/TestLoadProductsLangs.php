<?php

namespace App\Tests\Gt\Dao;

use Gt\Catalog\Dao\CatalogDao;
use Gt\Catalog\Data\ProductsFilter;
use Gt\Catalog\Form\ProductsFilterType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestLoadProductsLangs extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testLoad()
    {
        /** @var CatalogDao $catalogDao */
        $catalogDao = static::$kernel->getContainer()->get( 'gt.catalog.catalog_dao');

         $filter = new ProductsFilterType();

         $filter->setLikeName('White');



        $pls = $catalogDao->getProductsLangListByFilter($filter);
        $this->assertIsArray($pls);

        //products get make from langs :)

        $products=[];
        foreach ($pls as $pl)
        {
            $products[]=$pl->getProduct();
        }
        $this->assertIsArray($products);

    }

}