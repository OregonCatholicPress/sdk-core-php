<?php

use PayPal\Core\PPRequest;
use PayPal\Formatter\PPNVPFormatter;
use PHPUnit\Framework\TestCase;

class PPNVPFormatterTest extends TestCase
{
    private $object;

    #[Override]
    public function setup(): void
    {
        $this->object = new PPNVPFormatter();
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testValidSerializationCall()
    {
        $data = new MockNVPObject();
        $this->assertEquals(
            $data->toNVPString(),
            $this->object->toString(new PPRequest($data, 'NVP'))
        );
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testInvalidCall()
    {
        $this->expectException('BadMethodCallException');
        $this->object->toObject('somestring');
    }
}

class MockNVPObject
{
    public function toNVPString()
    {
        return 'dummy nvp string';
    }
}
