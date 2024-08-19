<?php


namespace App\Tests\Gt\Catalog;

use Catalog\B2b\Common\Data\Catalog\Package;
use Catalog\B2b\Common\Data\Catalog\Product;
use Gt\Catalog\Entity\PackageType;
use Gt\Catalog\Entity\ProductPackage;
use Gt\Catalog\Transformer\ProductTransformer;
use Gt\Catalog\Utils\MapBuilder;
use PHPUnit\Framework\TestCase;

class UpdateSpecialProductsTest extends TestCase
{
    /**
     * @dataProvider provideDataForUpdate
     *
     * @param PackageType[] $packagesTypes
     *
     * @param string[] $expectedFields
     */
    public function testUpdate(
        Product $dto,
        \Gt\Catalog\Entity\Product $product,
        array $packagesTypes,
        \Gt\Catalog\Entity\Product $expectedProduct,
        array $expectedFields,
        int $priority
    ) {
        /** @var PackageType[] $packagesTypesByCode */
        $packagesTypesByCode = MapBuilder::buildMap($packagesTypes, fn(PackageType $type) => $type->getCode());

        $updatedFields = ProductTransformer::updateSpecialProduct($dto, $product, $packagesTypesByCode, $priority );

        $this->assertEquals($expectedFields, $updatedFields);
        $this->assertEquals($expectedProduct, $product);
    }

    public static function provideDataForUpdate(): array
    {
        return [
            'test empty' => [
                'dto' => new Product(),
                'product' => new \Gt\Catalog\Entity\Product(),
                'packagesTypes' => [],
                'expectedProduct' => new \Gt\Catalog\Entity\Product(),
                'expectedFields' => [],
                'priority' => 0,
            ],
            'test real' => [
                'dto' =>
                    (new Product())
                        ->setSku('abc')
                        ->setWeight(4.5)
                        ->setWeightBruto(4.6)
                        ->setCodeFromCustom('123456')
                        ->setPackages(
                            [
                                (new Package())->setTypeCode('glass')->setWeight(0.1)
                            ]
                        )
                ,
                'product' => (new \Gt\Catalog\Entity\Product())
                    ->setSku('abc'),
                'packagesTypes' => [
                    (new PackageType())->setCode('glass')->setDescription('Stiklas')
                ],
                'expectedProduct' => (new \Gt\Catalog\Entity\Product())
                    ->setSku('abc')
                    ->setWeight(4.5)
                    ->setWeightBruto(4.6)
                    ->setCodeFromCustom('123456')
                    ->setPackages(
                        [
                            (new ProductPackage())->setPackageType(
                                (new PackageType())->setCode('glass')->setDescription('Stiklas')
                            )
                                ->setWeight(0.1)
                        ]
                    )
                ,
                'expectedFields' => ['weight', 'weight_bruto', 'code_from_custom', 'packages'],
                'priority' => 0,
            ],
        ];
    }

}