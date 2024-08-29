<?php

namespace App\Tests\Gt\Catalog;

use Catalog\B2b\Common\Data\Catalog\Package;
use Catalog\B2b\Common\Data\Catalog\Product;
use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\ClassificatorGroup;
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

        $updatedFields = ProductTransformer::updateSpecialProduct($dto, $product, $packagesTypesByCode, $priority);

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
                        ->setBarcode('1234560')
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
                    ->setProductsPackages(
                        [
                            (new ProductPackage())->setPackageType(
                                (new PackageType())->setCode('glass')->setDescription('Stiklas')
                            )
                                ->setWeight(0.1)
                        ]
                    )
                    ->setUpdatePriority(0)
                    ->setBarcode('1234560')
                ,
                'expectedFields' => ['weight', 'weight_bruto', 'code_from_custom', 'packages'],
                'priority' => 0,
            ],
            'test prioritized higher, override' => [
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
                    ->setSku('abc')
                    ->setWeight(3)
                    ->setWeightBruto(3)
                    ->setCodeFromCustom('123456')
                    ->setUpdatePriority(2)
                ,
                'packagesTypes' => [
                    (new PackageType())->setCode('glass')->setDescription('Stiklas')
                ],
                'expectedProduct' => (new \Gt\Catalog\Entity\Product())
                    ->setSku('abc')
                    ->setWeight(4.5)
                    ->setWeightBruto(4.6)
                    ->setCodeFromCustom('123456')
                    ->setProductsPackages(
                        [
                            (new ProductPackage())->setPackageType(
                                (new PackageType())->setCode('glass')->setDescription('Stiklas')
                            )
                                ->setWeight(0.1)
                        ]
                    )
                    ->setUpdatePriority(0)
                ,
                'expectedFields' => ['weight', 'weight_bruto', 'code_from_custom', 'packages'],
                'priority' => 0,
            ],
            'test prioritized lower valueless' => [
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
                    ->setSku('abc')
                    ->setUpdatePriority(1)
                ,
                'packagesTypes' => [
                    (new PackageType())->setCode('glass')->setDescription('Stiklas')
                ],
                'expectedProduct' => (new \Gt\Catalog\Entity\Product())
                    ->setSku('abc')
                    ->setWeight(4.5)
                    ->setWeightBruto(4.6)
                    ->setCodeFromCustom('123456')
                    ->setProductsPackages(
                        [
                            (new ProductPackage())->setPackageType(
                                (new PackageType())->setCode('glass')->setDescription('Stiklas')
                            )
                                ->setWeight(0.1)
                        ]
                    )
                    ->setUpdatePriority(1)
                ,
                'expectedFields' => ['weight', 'weight_bruto', 'code_from_custom', 'packages'],
                'priority' => 2,
            ],
            // ==============================================
            'test prioritized lower weight full' => [
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
                    ->setSku('abc')
                    ->setWeight(3)
                    ->setWeightBruto(3)
                    ->setCodeFromCustom('123')
                    ->setUpdatePriority(1)
                ,
                'packagesTypes' => [
                    (new PackageType())->setCode('glass')->setDescription('Stiklas')
                ],
                'expectedProduct' => (new \Gt\Catalog\Entity\Product())
                    ->setSku('abc')
                    ->setWeight(3)
                    ->setWeightBruto(3)
                    ->setCodeFromCustom('123')
                    ->setProductsPackages(
                        [
                            (new ProductPackage())->setPackageType(
                                (new PackageType())->setCode('glass')->setDescription('Stiklas')
                            )
                                ->setWeight(0.1)
                        ]
                    )
                    ->setUpdatePriority(1)
                ,
                'expectedFields' => ['packages'],
                'priority' => 2,
            ],

            // =============== packages changes ===================
            // let be logic like this:
            // if there are same packages, do not touch it
            // if different, then update only if the new priority is higher or equal ( <= )
            'test prioritized packages' => [
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
                    ->setSku('abc')
                    ->setWeight(3)
                    ->setWeightBruto(3)
                    ->setCodeFromCustom('123')
                    ->setUpdatePriority(1)
                    ->addProductPackage(
                        (new ProductPackage())
                            ->setPackageType(
                                (new PackageType())->setCode('glass')
                                    ->setDescription('Stiklas')
                            )
                            ->setWeight(0.3)

                    )
                ,
                'packagesTypes' => [
                    (new PackageType())->setCode('glass')->setDescription('Stiklas')
                ],
                'expectedProduct' => (new \Gt\Catalog\Entity\Product())
                    ->setSku('abc')
                    ->setWeight(4.5)
                    ->setWeightBruto(4.6)
                    ->setCodeFromCustom('123456')
                    ->setProductsPackages(
                        [
                            (new ProductPackage())->setPackageType(
                                (new PackageType())->setCode('glass')->setDescription('Stiklas')
                            )
                                ->setWeight(0.1)
                        ]
                    )
                    ->setUpdatePriority(1)
                ,
                'expectedFields' => ['weight', 'weight_bruto', 'code_from_custom', 'packages'],
                'priority' => 1,
            ],

            // ==========================================
            'test prioritized packages lower' => [
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
                    ->setSku('abc')
                    ->setWeight(3)
                    ->setWeightBruto(3)
                    ->setCodeFromCustom('123')
                    ->setUpdatePriority(1)
                    ->addProductPackage(
                        (new ProductPackage())
                            ->setPackageType(
                                (new PackageType())->setCode('glass')
                                    ->setDescription('Stiklas')
                            )
                            ->setWeight(0.3)

                    )
                ,
                'packagesTypes' => [
                    (new PackageType())->setCode('glass')->setDescription('Stiklas')
                ],
                'expectedProduct' => (new \Gt\Catalog\Entity\Product())
                    ->setSku('abc')
                    ->setWeight(3)
                    ->setWeightBruto(3)
                    ->setCodeFromCustom('123')
                    ->addProductPackage(
                        (new ProductPackage())->setPackageType(
                            (new PackageType())->setCode('glass')->setDescription('Stiklas')
                        )
                            ->setWeight(0.3)

                    )
                    ->setUpdatePriority(1)
                ,
                'expectedFields' => [],
                'priority' => 2,
            ],
            // ========================
            'test prioritized packages lower empty' => [
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
                    ->setSku('abc')
                    ->setWeight(3)
                    ->setWeightBruto(3)
                    ->setCodeFromCustom('123')
                    ->setUpdatePriority(1)
                ,
                'packagesTypes' => [
                    (new PackageType())->setCode('glass')->setDescription('Stiklas')
                ],
                'expectedProduct' => (new \Gt\Catalog\Entity\Product())
                    ->setSku('abc')
                    ->setWeight(3)
                    ->setWeightBruto(3)
                    ->setCodeFromCustom('123')
                    ->addProductPackage(
                        (new ProductPackage())->setPackageType(
                            (new PackageType())->setCode('glass')->setDescription('Stiklas')
                        )
                            ->setWeight(0.1)

                    )
                    ->setUpdatePriority(1)
                ,
                'expectedFields' => ['packages'],
                'priority' => 2,
            ],

            'test barcode with priority' => [
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
                        ->setBarcode('1234560')

                ,
                'product' => (new \Gt\Catalog\Entity\Product())
                    ->setSku('abc')
                    ->setUpdatePriority(10)
                ,
                'packagesTypes' => [
                    (new PackageType())->setCode('glass')->setDescription('Stiklas')
                ],
                'expectedProduct' => (new \Gt\Catalog\Entity\Product())
                    ->setSku('abc')
                    ->setWeight(4.5)
                    ->setWeightBruto(4.6)
                    ->setCodeFromCustom('123456')
                    ->setProductsPackages(
                        [
                            (new ProductPackage())->setPackageType(
                                (new PackageType())->setCode('glass')->setDescription('Stiklas')
                            )
                                ->setWeight(0.1)
                        ]
                    )
                    ->setUpdatePriority(1)
                    ->setBarcode('1234560')
                ,
                'expectedFields' => ['weight', 'weight_bruto', 'code_from_custom', 'packages'],
                'priority' => 1,
            ],
            'test measure' => [
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
                        ->setBarcode('1234560')
                        ->setMeasure('VNT')
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
                    ->setProductsPackages(
                        [
                            (new ProductPackage())->setPackageType(
                                (new PackageType())->setCode('glass')->setDescription('Stiklas')
                            )
                                ->setWeight(0.1)
                        ]
                    )
                    ->setUpdatePriority(0)
                    ->setBarcode('1234560')
                    ->setMeasure(
                        (new Classificator())
                            ->setCode('vnt')
                            ->setClassificatorGroup((new ClassificatorGroup())->setCode('measure'))
                    )
                ,
                'expectedFields' => ['weight', 'weight_bruto', 'code_from_custom', 'packages'],
                'priority' => 0,
            ],

        ];
    }
}