<?php

use PayPal\Core\PPHttpConfig;
use PayPal\Core\PPRequest;
use PayPal\Handler\PPGenericServiceHandler;
use PHPUnit\Framework\TestCase;

class PPGenericServiceHandlerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {

    }

    #[Override]
    protected function tearDown(): void
    {

    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testHeadersAdded()
    {
        $bindingType = 'bindingType';
        $devEmail = 'developer@domain.com';

        $httpConfig = new PPHttpConfig();
        $handler = new PPGenericServiceHandler('sdkname', 'sdkversion');
        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), $bindingType),
            ['config' => ['service.SandboxEmailAddress' => $devEmail]]
        );

        $headers = $httpConfig->getHeaders();
        $this->assertEquals(6, count($headers));
        $this->assertArrayHasKey('X-PAYPAL-DEVICE-IPADDRESS', $headers);
        $this->assertArrayHasKey('X-PAYPAL-REQUEST-SOURCE', $headers);
        $this->assertEquals($bindingType, $headers['X-PAYPAL-REQUEST-DATA-FORMAT']);
        $this->assertEquals($bindingType, $headers['X-PAYPAL-RESPONSE-DATA-FORMAT']);
        $this->assertEquals($devEmail, $headers['X-PAYPAL-SANDBOX-EMAIL-ADDRESS']);

    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testSourceHeader()
    {
        $httpConfig = new PPHttpConfig();
        $handler = new PPGenericServiceHandler('sdkname', 'sdkversion');
        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), 'NV'),
            ['config' => []]
        );

        $headers = $httpConfig->getHeaders();
        $this->assertArrayHasKey('X-PAYPAL-REQUEST-SOURCE', $headers);
        $this->assertMatchesRegularExpression('/.*sdkname.*/', $headers['X-PAYPAL-REQUEST-SOURCE']);
        $this->assertMatchesRegularExpression('/.*sdkversion.*/', $headers['X-PAYPAL-REQUEST-SOURCE']);
    }
}
