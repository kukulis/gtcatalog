<?php

namespace App\Tests\Gt\Catalog\Utils;

use Gt\Catalog\Entity\Language;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Services\Rest\ProductsRestService;
use PHPUnit\Framework\TestCase;

class BuildAdditionalLanguagesDataTest extends TestCase
{
    public function testBuild()
    {

        $productLanguages = [
            (new ProductLanguage())->setName('aaa')
                ->setProduct((new Product())->setSku('123'))
                ->setLanguage((new Language())->setCode('en')),
            (new ProductLanguage())->setName('bbb')
                ->setProduct((new Product())->setSku('123'))
                ->setLanguage((new Language())->setCode('lt')),
            (new ProductLanguage())->setName('ccc')
                ->setProduct((new Product())->setSku('456'))
                ->setLanguage((new Language())->setCode('en')),
            (new ProductLanguage())->setName('ddd')
                ->setProduct((new Product())->setSku('456'))
                ->setLanguage((new Language())->setCode('lt')),
        ];


        $expected = [
            '123' => [
                'en' => 'aaa',
                'lt' => 'bbb',
            ],
            '456' => [
                'en' => 'ccc',
                'lt' => 'ddd',
            ],
        ];

        $additionalLanguagesData = ProductsRestService::buildAdditionalLanguagesData($productLanguages);
        $this->assertEquals($expected, $additionalLanguagesData);
    }

}