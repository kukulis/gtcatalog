<?php
/**
 * CategoriesHelperTest.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-14
 * Time: 11:46
 */

namespace App\Tests\Gt\Catalog\Utils;


use Gt\Catalog\Utils\CategoriesHelper;
use PHPUnit\Framework\TestCase;

class CategoriesHelperTest extends TestCase
{

    public function testSplit() {
        $this->assertEquals( ['aaa'], CategoriesHelper::splitCategoriesStr('aaa') );
        $this->assertEquals( ['aaa', 'bbb'], CategoriesHelper::splitCategoriesStr(' aaa bbb ') );
        $this->assertEquals( ['aaa', 'bbb', 'ccc'], CategoriesHelper::splitCategoriesStr('aaa   bbb ccc') );
        $this->assertEquals( ['aa-aa', 'bb__b', 'ccc-c'], CategoriesHelper::splitCategoriesStr('aa-aa   bb__b ccc-c') );

        $this->assertEquals( [], CategoriesHelper::splitCategoriesStr(' ') );
        $this->assertEquals( [], CategoriesHelper::splitCategoriesStr(null) );
    }

    public function testValidateCategoryCode() {
        $this->assertTrue(CategoriesHelper::validateCategoryCode('aaa'));
        $this->assertTrue(CategoriesHelper::validateCategoryCode('aaa_a'));
        $this->assertTrue(CategoriesHelper::validateCategoryCode('aa-a'));
        $this->assertTrue(CategoriesHelper::validateCategoryCode('a-a-a'));
        $this->assertTrue(CategoriesHelper::validateCategoryCode('a_a_a'));

        $this->assertFalse(CategoriesHelper::validateCategoryCode('a a_a'));
        $this->assertFalse(CategoriesHelper::validateCategoryCode(''));
        $this->assertFalse(CategoriesHelper::validateCategoryCode(null));
        $this->assertFalse(CategoriesHelper::validateCategoryCode('AA'));
        $this->assertFalse(CategoriesHelper::validateCategoryCode('žž'));
        $this->assertFalse(CategoriesHelper::validateCategoryCode('z,z'));
    }

    public function testSplitTags() {
        $this->assertEquals( ['vienas', 'du', 'trys'],  CategoriesHelper::splitTagsStr( 'vienas, du, trys' ) );
        $this->assertEquals( ['vienas', 'du', 'trys'],  CategoriesHelper::splitTagsStr( 'vienas du trys' ) );
        $this->assertEquals( ['vienas', 'du', 'trys'],  CategoriesHelper::splitTagsStr( 'vienas,du,trys' ) );
        $this->assertEquals( [],  CategoriesHelper::splitTagsStr( null ) );

    }

}