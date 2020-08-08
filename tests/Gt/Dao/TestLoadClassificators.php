<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.5
 * Time: 22.51
 */

namespace App\Tests\Gt\Dao;


use Gt\Catalog\Dao\CatalogDao;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestLoadClassificators extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
    }

    public function testLoadClassificatorsList() {
        $container = static::$kernel->getContainer();

        /** @var CatalogDao $catalogDao */
        $catalogDao = $container->get('gt.catalog.catalog_dao');

        $classificators = $catalogDao->loadClassificatorsList(['aaa', 'bbb']);
        $this->assertNotEmpty( $classificators );
    }

}