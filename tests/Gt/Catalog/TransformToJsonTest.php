<?php

namespace App\Tests\Gt\Catalog;

use Catalog\B2b\Common\Data\Catalog\Product;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;

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
            ]
        ];
    }

// TODO transform from json to objects.
}