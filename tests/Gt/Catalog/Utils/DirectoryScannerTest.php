<?php
/**
 * DirectoryScannerTest.php
 * Created by Giedrius Tumelis.
 * Date: 2021-02-15
 * Time: 13:43
 */

namespace App\Tests\Gt\Catalog\Utils;

use Gt\Catalog\Utils\ClosureFilesScanListener;
use Gt\Catalog\Utils\PicturesHelper;
use Gt\Catalog\Utils\RecursiveFilesScanner;
use PHPUnit\Framework\TestCase;

class DirectoryScannerTest extends TestCase
{
    public function testScanJobs() {

        $curDir = realpath('.');
        echo "curDir=$curDir\n";
        $dir = realpath('jobs');
        echo "The dir is $dir\n";
        $this->assertTrue(is_dir($dir));


        $listener = new ClosureFilesScanListener(
            function($file) {
                echo "found file $file \n";
            }
        );
        $scanner = new RecursiveFilesScanner($dir);
        $scanner->scan($listener);
    }

    public function testScanImages() {
//        $curDir = realpath('.');
//        echo "curDir=$curDir\n";
        $dir = realpath('img');
        echo "The dir is $dir\n";
        $this->assertTrue(is_dir($dir));


        $listener = new ClosureFilesScanListener(
            function($file) {
                $id = PicturesHelper::getIdFromPictureDir(dirname($file));
                echo "Id=$id\n";
            }
        );

        $scanner = new RecursiveFilesScanner($dir);
        $scanner->scan($listener);
    }
}
