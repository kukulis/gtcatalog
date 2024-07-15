<?php

namespace App\Tests\Gt\Catalog;

use Catalog\B2b\Common\Data\Catalog\Package;
use Catalog\B2b\Common\Data\Catalog\Product;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use DateTime;

class TransformToJsonTest extends TestCase
{

    /**
     * @param Product[] $products
     *
     * @dataProvider provideDataFoObjectToJson
     */
    public function testObjectToJson( /* may be should use restResult class? */ array $products,
        int $testedIndex,
        array $productJsonArray
    ) {
        $serializer = SerializerBuilder::create()->build();

        $allProductsArray = $serializer->toArray($products);

//        $json = $serializer->serialize($products, 'json');
//        echo "Json=".$json."\n";

        $this->assertArrayHasKey($testedIndex, $allProductsArray);

        $singleProductArray = $allProductsArray[$testedIndex];

        $this->assertEquals($productJsonArray, $singleProductArray);
    }

    public static function provideDataFoObjectToJson(): array
    {
        return [
            'test1' => [
                'products' => [
                    (new Product())
                        ->setSku('abc123')
                        ->setWeight(10)
                        ->setWeightBruto(10.5)
                ],
                'testedIndex' => 0,

                'productJsonArray' => [
                    'sku' => 'abc123',
                    'weight' => 10.0,
                    'weight_bruto' => 10.5,
                    'version' => 0,
                    'for_male' => false,
                    'for_female' => false,
                    'length' => 0.0,
                    'height' => 0.0,
                    'width' => 0.0,
                    'google_product_category_id' => 0,
                    'tags' => '',
                    'stock' => 0,
                ]
            ],

            'test with packages' => [
                'products' => [
                    (new Product())
                        ->setSku('abc123')
                        ->setWeight(10)
                        ->setWeightBruto(10.5)
                        ->setPackages(
                            [
                                (new Package())
                                    ->setTypeCode('glass')
                                    ->setWeight(7.5)
                            ]
                        ),
                ],
                'testedIndex' => 0,
                'productJsonArray' => [
                    'sku' => 'abc123',
                    'weight' => 10.0,
                    'weight_bruto' => 10.5,
                    'version' => 0,
                    'for_male' => false,
                    'for_female' => false,
                    'length' => 0.0,
                    'height' => 0.0,
                    'width' => 0.0,
                    'google_product_category_id' => 0,
                    'tags' => '',
                    'stock' => 0,
                    'packages' => [
                        [
                            'type_code' => 'glass',
                            'weight' => 7.5
                        ]
                    ]
                ]
            ],
            'test with date' => [
                'products' => [
                    (new Product())
                        ->setSku('abc123')
                        ->setWeight(10)
                        ->setWeightBruto(10.5)
                        ->setLastUpdate(DateTime::createFromFormat('Y-m-d H:i:s', '2024-06-01 12:15:00'))
                ],
                'testedIndex' => 0,

                'productJsonArray' => [
                    'sku' => 'abc123',
                    'weight' => 10.0,
                    'weight_bruto' => 10.5,
                    'version' => 0,
                    'for_male' => false,
                    'for_female' => false,
                    'length' => 0.0,
                    'height' => 0.0,
                    'width' => 0.0,
                    'google_product_category_id' => 0,
                    'tags' => '',
                    'stock' => 0,
                    'last_update' => '2024-06-01 12:15:00'
                ]
            ],
        ];
    }

}