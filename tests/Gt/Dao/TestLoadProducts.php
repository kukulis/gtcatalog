<?php

namespace App\Tests\Gt\Dao;

use Doctrine\ORM\EntityManager;
use Gt\Catalog\Dao\CatalogDao;
use Gt\Catalog\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestLoadProducts extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testLoad()
    {

        /** @var CatalogDao $catalogDao */
        $catalogDao = static::getContainer()->get('gt.catalog.catalog_dao');

        $doctrine = $catalogDao->getDoctrine();
        /** @var EntityManager $em */
        $em = $doctrine->getManager();

        $builder =  $em->createQueryBuilder();
        $builder->select('p')
            ->from(Product::class, 'p');

        $builder->setMaxResults( 5 );
        $this->assertTrue(true);

        /** @var Product[] $products */
        $products = $builder->getQuery()->getResult();

        $this->assertIsArray($products);

        var_dump($products);

    }
}