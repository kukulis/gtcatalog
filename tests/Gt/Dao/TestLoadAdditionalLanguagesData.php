<?php

namespace App\Tests\Gt\Dao;

use Gt\Catalog\Dao\CatalogDao;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestLoadAdditionalLanguagesData extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testLoadLangs()
    {
        $skus = ['0000-01660', '0000-02063'];

        $container = static::$kernel->getContainer();

        /** @var CatalogDao $catalogDao */
        $catalogDao = $container->get( 'gt.catalog.catalog_dao' );

        $additionalLanguagesData = $catalogDao->loadAdditionLanguagesData($skus, ['*']);

        $this->assertIsArray($additionalLanguagesData);
        $this->assertGreaterThan(0, count($additionalLanguagesData));
    }
}