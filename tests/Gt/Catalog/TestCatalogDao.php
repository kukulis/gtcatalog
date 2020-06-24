<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 22.56
 */

namespace App\Tests\Gt\Catalog;


use Gt\Catalog\Dao\CatalogDao;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestCatalogDao extends KernelTestCase
{
    protected function setUp() {
        self::bootKernel();
    }

    public function testGetProductsList() {
        $container = static::$kernel->getContainer();

        /** @var CatalogDao $catalogDao */
        $catalogDao = $container->get('gt.catalog.catalog_dao');


        $products = $catalogDao->getProductsList(0, 10 );

        $this->assertGreaterThan(0, count($products));

        $this->assertTrue(is_string($products[0]->getSku()));

        echo "Sku=".$products[0]->getSku()."\n";
    }

}