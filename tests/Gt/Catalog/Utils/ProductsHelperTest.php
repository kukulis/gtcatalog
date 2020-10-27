<?php
/**
 * ProductsHelperTest.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-27
 * Time: 09:44
 */

namespace App\Tests\Gt\Catalog\Utils;


use Gt\Catalog\Utils\ProductsHelper;
use PHPUnit\Framework\TestCase;


class ProductsHelperTest extends TestCase
{

    public function testFixCode() {
        $this->assertEquals('dazytiems-plaukams', ProductsHelper::fixCode('Dažytiems plaukams' ));
        $this->assertEquals('d-fi', ProductsHelper::fixCode("D:fi"));
        $this->assertEquals('d-a-t-e-', ProductsHelper::fixCode("d.a.t.e."));
        $this->assertEquals('d-link', ProductsHelper::fixCode("D-Link"));
        $this->assertEquals('coty-l-aimant', ProductsHelper::fixCode("Coty L'aimant"));
        $this->assertEquals('chanson-d-eau', ProductsHelper::fixCode("Chanson D'Eau"));
        $this->assertEquals('bumble-bumble', ProductsHelper::fixCode("Bumble & Bumble"));
        $this->assertEquals('brian-dales-ltb', ProductsHelper::fixCode("Brian Dales & ltb"));
        $this->assertEquals('bellapierre', ProductsHelper::fixCode("Bellápierre"));
        $this->assertEquals('ziliems-plaukams', ProductsHelper::fixCode("Žiliems plaukams"));
        $this->assertEquals('a+b', ProductsHelper::fixCode('a+b'));
    }

    public function testPreg() {
        preg_match_all('/[^[:alnum:]+]+/', 'dazytiems plaukams,+', $matches );

        echo "Matches:[".join ( "|", $matches[0])."]";
        $this->assertTrue(true);
    }
}