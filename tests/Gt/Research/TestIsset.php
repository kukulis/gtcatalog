<?php
/**
 * TestIsset.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-26
 * Time: 14:52
 */

namespace App\Tests\Gt\Research;

use Gt\Catalog\Services\Legacy\TmpClassificator;
use PHPUnit\Framework\TestCase;

class TestIsset extends TestCase
{
    public function testIsset() {
        $c = new TmpClassificator();
        $c->setTest('aaa');
        $c->value='bbb';

        $this->assertTrue(isset($c->value));
        $test = 'test';
        $this->assertFalse(isset($c->{$test}));
    }

}