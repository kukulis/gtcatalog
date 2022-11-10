<?php

namespace App\Tests\Gt\Dao;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestLoadProducts extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testLoad()
    {
        $this->assertTrue(true);
    }
}