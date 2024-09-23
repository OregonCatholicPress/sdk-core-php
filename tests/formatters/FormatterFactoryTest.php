<?php

use PayPal\Formatter\FormatterFactory;
use PHPUnit\Framework\TestCase;

class FormatterFactoryTest extends TestCase
{
    #[PHPUnit\Framework\Attributes\Test]
    public function testValidBinding()
    {
        $this->assertEquals(PayPal\Formatter\PPNVPFormatter::class, FormatterFactory::factory('NV')::class);
        $this->assertEquals(PayPal\Formatter\PPSOAPFormatter::class, FormatterFactory::factory('SOAP')::class);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testInvalidBinding()
    {
        $this->expectException('\InvalidArgumentException');
        FormatterFactory::factory('Unknown');
    }
}
