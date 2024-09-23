<?php

use PayPal\Common\PPApiContext;
use PayPal\Core\PPConstants;
use PayPal\Core\PPHttpConfig;
use PayPal\Handler\PPOpenIdHandler;
use PHPUnit\Framework\TestCase;

class PPOpenIdHandlerTest extends TestCase
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
    public function testInvalidConfiguration()
    {
        $httpConfig = new PPHttpConfig();
        $apiContext = new PPApiContext(['mode' => 'unknown', 'acct1.ClientId' => 'clientId', 'acct1.ClientSecret' => 'clientSecret']);
        $handler = new PPOpenIdHandler();

        $this->expectException(PayPal\Exception\PPConfigurationException::class);
        $handler->handle($httpConfig, 'payload', ['path' => '/path', 'apiContext' => $apiContext]);

        $httpConfig = new PPHttpConfig();
        $apiContext = new PPApiContext(['acct1.ClientId' => 'clientId', 'acct1.ClientSecret' => 'clientSecret']);
        $handler = new PPOpenIdHandler($apiContext);

        $this->expectException(PayPal\Exception\PPConfigurationException::class);
        $handler->handle($httpConfig, 'payload', ['path' => '/path', 'apiContext' => $apiContext]);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testHeadersAdded()
    {
        $httpConfig = new PPHttpConfig();
        $apiContext = new PPApiContext(['mode' => 'sandbox', 'acct1.ClientId' => 'clientId', 'acct1.ClientSecret' => 'clientSecret']);

        $handler = new PPOpenIdHandler();
        $handler->handle($httpConfig, 'payload', ['apiContext' => $apiContext]);

        $this->assertArrayHasKey('Authorization', $httpConfig->getHeaders());
        $this->assertArrayHasKey('User-Agent', $httpConfig->getHeaders());
        $this->assertStringContainsString('PayPalSDK', $httpConfig->getHeader('User-Agent'));
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testModeBasedEndpoint()
    {
        $httpConfig = new PPHttpConfig();
        $apiContext = new PPApiContext(['mode' => 'sandbox', 'acct1.ClientId' => 'clientId', 'acct1.ClientSecret' => 'clientSecret']);
        $handler = new PPOpenIdHandler();

        $handler->handle($httpConfig, 'payload', ['path' => '/path', 'apiContext' => $apiContext]);
        $this->assertEquals(PPConstants::REST_SANDBOX_ENDPOINT . "path", $httpConfig->getUrl());

        $httpConfig = new PPHttpConfig();
        $apiContext = new PPApiContext(['mode' => 'live', 'acct1.ClientId' => 'clientId', 'acct1.ClientSecret' => 'clientSecret']);
        $handler = new PPOpenIdHandler();

        $handler->handle($httpConfig, 'payload', ['path' => '/path', 'apiContext' => $apiContext]);
        $this->assertEquals(PPConstants::REST_LIVE_ENDPOINT . "path", $httpConfig->getUrl());
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testCustomEndpoint()
    {
        $customEndpoint = 'http://mydomain';
        $httpConfig = new PPHttpConfig();
        $apiContext = new PPApiContext(['service.EndPoint' => $customEndpoint, 'acct1.ClientId' => 'clientId', 'acct1.ClientSecret' => 'clientSecret']);
        $handler = new PPOpenIdHandler();

        $handler->handle($httpConfig, 'payload', ['path' => '/path', 'apiContext' => $apiContext]);
        $this->assertEquals("$customEndpoint/path", $httpConfig->getUrl());

        $customEndpoint = 'http://mydomain/';
        $httpConfig = new PPHttpConfig();
        $apiContext = new PPApiContext(['service.EndPoint' => $customEndpoint, 'acct1.ClientId' => 'clientId', 'acct1.ClientSecret' => 'clientSecret']);
        $handler = new PPOpenIdHandler();

        $handler->handle($httpConfig, 'payload', ['path' => '/path', 'apiContext' => $apiContext]);
        $this->assertEquals("{$customEndpoint}path", $httpConfig->getUrl());

        $customEndpoint = 'http://mydomain';
        $httpConfig = new PPHttpConfig();
        $apiContext = new PPApiContext(['service.EndPoint' => 'xyz', 'openid.EndPoint' => $customEndpoint, 'acct1.ClientId' => 'clientId', 'acct1.ClientSecret' => 'clientSecret']);
        $handler = new PPOpenIdHandler();

        $handler->handle($httpConfig, 'payload', ['path' => '/path', 'apiContext' => $apiContext]);
        $this->assertEquals("$customEndpoint/path", $httpConfig->getUrl());
    }
}
