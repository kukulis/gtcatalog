<?php

namespace App\Tests\Gt\Catalog;

use Catalog\B2b\Common\Data\Catalog\Package;
use Catalog\B2b\Common\Data\Catalog\Product;
use Gt\Catalog\Entity\PackageType;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Entity\ProductPackage;
use Gt\Catalog\Transformer\ProductTransformer;
use PHPUnit\Framework\TestCase;

class TransformToRestTest extends TestCase
{
    /**
     * @dataProvider provideProducts
     */
    public function testTransform(ProductLanguage $productLanguage, Product $expectedProduct)
    {
        $productTransformer = new ProductTransformer();

        $product = $productTransformer->transformToRestProduct($productLanguage);

        $this->assertEquals($expectedProduct, $product);
    }

    public function provideProducts(): array
    {
        return [
            'test 1' => [
                'productLanguage' => (new ProductLanguage())->setProduct(new \Gt\Catalog\Entity\Product()),
                'expectedProduct' => new Product(),
            ],
            'test 2' => [
                'productLanguage' => (new ProductLanguage())->setProduct(
                    (new \Gt\Catalog\Entity\Product())->setProductsPackages(
                        [
                            (new ProductPackage())
                                ->setPackageType((new PackageType())->setCode('glass')->setDescription('Stiklas'))
                                ->setWeight(1.7)
                        ]
                    )
                        ->setSku('ABC')
                ),
                'expectedProduct' => (new Product())
                    ->setSku('ABC')
                    ->setEan('ABC')
                    ->setPackages(
                        [
                            (new Package())
                                ->setTypeCode('glass')
                                ->setWeight(1.7)
                        ]
                    )
            ],
        ];
    }

}