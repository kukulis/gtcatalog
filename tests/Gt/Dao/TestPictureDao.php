<?php
/**
 * TestPictureDao.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-12
 * Time: 10:42
 */

namespace App\Tests\Gt\Dao;


use Gt\Catalog\Dao\PicturesDao;
use Gt\Catalog\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestPictureDao extends KernelTestCase
{
    protected function setUp()
    {
        self::bootKernel();
    }

    public function testCreatePicture() {
        $container = static::$kernel->getContainer();

        /** @var PicturesDao $picturesDao */
        $picturesDao = $container->get('gt.catalog.pictures_dao' );

        $picture = new Picture();
        $picture->setName('aaa.jpg');
        $p = $picturesDao->insertPicture($picture);

        $this->assertNotEmpty($p->getId());

        echo "id=".$p->getId()."\n";
    }

}