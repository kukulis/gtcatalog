<?php

namespace App\Tests\Gt\Forms;

use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductLanguage;
use Gt\Catalog\Form\ProductFormType;
use PHPUnit\Framework\TestCase;

class ProductFormTypeTest extends TestCase
{

    public function testGetters () {
        $pft = new ProductFormType();

        $product = new Product();
        $productLanguage = new ProductLanguage();

        $pft->setProduct($product);
        $pft->setProductLanguage($productLanguage);

        $product->setSku('aaa');

        $this->assertEquals( 'aaa', $pft->getPSku());

        $pft->setPLName('Grybas');

        $this->assertEquals( 'Grybas',  $productLanguage->getName());
    }

}