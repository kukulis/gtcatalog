<?php

namespace App\Tests\Gt\Dao;

use Gt\Catalog\Dao\CatalogDao;
use Gt\Catalog\Services\Rest\ProductsRestService;
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

        $productLanguages = $catalogDao->loadProductLanguagesLazy($skus, ['*']);

        $this->assertIsArray($productLanguages);
        $this->assertGreaterThan(0, count($productLanguages));

        $additionalLanguagesData = ProductsRestService::buildAdditionalNameLanguageData($productLanguages);

        $this->assertArrayHasKey('0000-01660', $additionalLanguagesData);
        $this->assertArrayHasKey('0000-02063', $additionalLanguagesData);

        $this->assertArrayHasKey('en', $additionalLanguagesData['0000-01660']);
        $this->assertArrayHasKey('lt', $additionalLanguagesData['0000-01660']);
    }
    public function testLoadPartLangs()
    {
        $skus = ['0000-01660', '0000-02063'];

        $container = static::$kernel->getContainer();

        /** @var CatalogDao $catalogDao */
        $catalogDao = $container->get( 'gt.catalog.catalog_dao' );

        $productLanguages = $catalogDao->loadProductLanguagesLazy($skus, ['lt']);

        $this->assertIsArray($productLanguages);
        $this->assertGreaterThan(0, count($productLanguages));

        $additionalLanguagesData = ProductsRestService::buildAdditionalNameLanguageData($productLanguages);

        $this->assertArrayHasKey('0000-01660', $additionalLanguagesData);
        $this->assertArrayHasKey('0000-02063', $additionalLanguagesData);

        $this->assertArrayNotHasKey('en', $additionalLanguagesData['0000-01660']);
        $this->assertArrayHasKey('lt', $additionalLanguagesData['0000-01660']);

        $this->assertCount(1, $additionalLanguagesData['0000-01660']);
    }
    public function testLoadNoLangs()
    {
        $skus = ['0000-01660', '0000-02063'];

        $container = static::$kernel->getContainer();

        /** @var CatalogDao $catalogDao */
        $catalogDao = $container->get( 'gt.catalog.catalog_dao' );

        $productLanguages = $catalogDao->loadProductLanguagesLazy($skus, []);

        $this->assertIsArray($productLanguages);
        $additionalLanguagesData = ProductsRestService::buildAdditionalNameLanguageData($productLanguages);
        $this->assertCount(0, $additionalLanguagesData);
    }
}