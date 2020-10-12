<?php
/**
 * PicturesHelperTest.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-12
 * Time: 10:17
 */

namespace App\Tests\Gt\Catalog\Utils;


use Gt\Catalog\Utils\PicturesHelper;
use PHPUnit\Framework\TestCase;

class PicturesHelperTest extends TestCase
{
    public function testCanonizePictureName() {
        $this->assertEquals ( 'aaa.jpg', PicturesHelper::canonizePictureName('aaa.jpg'));
        $this->assertEquals ( 'aa_a.jpg', PicturesHelper::canonizePictureName('aa a.jpg'));
        $this->assertEquals ( 'Vi_nas_Du.jpg', PicturesHelper::canonizePictureName('Viėnasąęėįšųūž„Du.jpg'));
        $this->assertEquals ( 'Vienas_Du.jpg', PicturesHelper::canonizePictureName('Vienasąęėįšųūž„Du.jpg'));
    }
}