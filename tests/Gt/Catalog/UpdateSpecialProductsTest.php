<?php


namespace App\Tests\Gt\Catalog;

use Catalog\B2b\Common\Data\Catalog\Product;
use Gt\Catalog\Transformer\ProductTransformer;
use PHPUnit\Framework\TestCase;

class UpdateSpecialProductsTest extends TestCase
{
    /**
     * @dataProvider provideDataForUpdate
     *
     * @param string[] $expectedFields
     */
    public function testUpdate(Product $dto, \Gt\Catalog\Entity\Product $product, \Gt\Catalog\Entity\Product $expectedProduct, array $expectedFields ) {
        $updatedFields = ProductTransformer::updateSpecialProduct($dto, $product);

        $this->assertEquals($expectedFields, $updatedFields);
        $this->assertEquals($expectedProduct, $product);
    }

    public static function provideDataForUpdate() : array {
        return [
            'test empty' => [
                'dto' => new Product(),
                'product' => new \Gt\Catalog\Entity\Product(),
                'expectedProduct' => new \Gt\Catalog\Entity\Product(),
                'expectedFields' => []
            ],

            // TODO real data
        ];
    }

}