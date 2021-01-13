<?php
/**
 * FilesHelperTest.php
 * Created by Giedrius Tumelis.
 * Date: 2021-01-13
 * Time: 16:30
 */

namespace App\Tests\Gt\Catalog\Utils;


use Gt\Catalog\Utils\FileHelper;
use PHPUnit\Framework\TestCase;

class FilesHelperTest extends TestCase
{
    public function testScan () {
        $files = FileHelper::getFiles('/media/disk/www/gtcatalog/jobs/5/tmp');
        $this->assertGreaterThan(2, $files);

        var_dump($files);
    }
}