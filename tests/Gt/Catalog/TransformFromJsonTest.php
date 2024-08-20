<?php

namespace App\Tests\Gt\Catalog;

use Catalog\B2b\Common\Data\Catalog\Package;
use Catalog\B2b\Common\Data\Catalog\Product;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

class TransformFromJsonTest extends TestCase
{

    /**
     * @param string $json
     * @param Product[] $expectedProducts
     *
     * @dataProvider getJsonData
     */
    public function testTransform(string $json, array $expectedProducts) {
        $serializer = SerializerBuilder::create()->build();

        $products = $serializer->deserialize($json, sprintf('array<%s>', Product::class ),  'json' );

        $this->assertEquals($expectedProducts, $products);
    }


    public static function getJsonData(): array
    {
        return [
            'test1' => [
                'json' => '[{"sku":"abc123","version":0,"for_male":false,"for_female":false,"weight":10.0,"weight_bruto":10.5,"length":0.0,"height":0.0,"width":0.0,"google_product_category_id":0,"tags":"","stock":0}]',
                'expectedProducts' => [
                    (new Product())
                        ->setSku('abc123')
                        ->setWeight(10.0)
                        ->setWeightBruto(10.5)
                ]
            ],
            'test2' => [
                'json' => '[{"sku":"abc123","version":0,"for_male":false,"for_female":false,"weight":10.0,"weight_bruto":10.5,"length":0.0,"height":0.0,"width":0.0,"google_product_category_id":0,"tags":"","stock":0,"packages":[{"type_code":"glass","weight":7.5}]}]',
                'expectedProducts' => [
                    (new Product())
                        ->setSku('abc123')
                        ->setWeight(10.0)
                        ->setWeightBruto(10.5)
                        ->setPackages(
                            [
                                (new Package())
                                    ->setTypeCode('glass')
                                    ->setWeight(7.5)
                            ]
                        ),
                ],
            ]
        ];
    }
}