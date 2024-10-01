<?php

use PayPal\Auth\PPSignatureCredential;
use PayPal\Core\PPConstants;
use PayPal\Core\PPHttpConfig;
use PayPal\Core\PPRequest;
use PayPal\Handler\PPPlatformServiceHandler;
use PHPUnit\Framework\TestCase;

class PPPlatformServiceHandlerTest extends TestCase
{
    private $options;

    #[Override]
    protected function setUp(): void
    {
        $this->options = ['config' => ['mode' => 'sandbox', 'acct1.UserName' => 'user', 'acct1.Password' => 'pass', 'acct1.Signature' => 'sign', 'acct1.AppId' => 'APP', 'acct2.UserName' => 'certuser', 'acct2.Password' => 'pass', 'acct2.CertPath' => 'pathtocert'], 'serviceName' => 'AdaptivePayments', 'apiMethod' => 'ConvertCurrency'];
    }

    #[Override]
    protected function tearDown(): void
    {

    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testDefaultAPIAccount()
    {

        $req = new PPRequest(new stdClass(), 'NV');

        $httpConfig = new PPHttpConfig();
        $handler = new PPPlatformServiceHandler(null, 'sdkname', 'sdkversion');
        $handler->handle($httpConfig, $req, $this->options);
        $this->assertEquals($this->options['config']['acct1.Signature'], $req->getCredential()->getSignature());

        $cred = new PPSignatureCredential('user', 'pass', 'sig');
        $cred->setApplicationId('appId');

        $httpConfig = new PPHttpConfig();
        $handler = new PPPlatformServiceHandler($cred, 'sdkname', 'sdkversion');
        $handler->handle($httpConfig, $req, $this->options);

        $this->assertEquals($cred, $req->getCredential());
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testHeadersAdded()
    {

        $req = new PPRequest(new stdClass(), 'NV');

        $httpConfig = new PPHttpConfig();
        $handler = new PPPlatformServiceHandler(null, 'sdkname', 'sdkversion');
        $handler->handle($httpConfig, $req, $this->options);

        $this->assertEquals(9, count($httpConfig->getHeaders()), "Basic headers not added");

        $httpConfig = new PPHttpConfig();
        $handler = new PPPlatformServiceHandler('certuser', 'sdkname', 'sdkversion');
        $handler->handle($httpConfig, $req, $this->options);

        $this->assertEquals(7, count($httpConfig->getHeaders()));
        $this->assertEquals('certuser', $req->getCredential()->getUsername());
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testEndpoint()
    {
        $serviceName = 'AdaptivePayments';
        $apiMethod = 'ConvertCurrency';

        $httpConfig = new PPHttpConfig();
        $handler = new PPPlatformServiceHandler(null, 'sdkname', 'sdkversion');

        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), 'NV'),
            $this->options
        );
        $this->assertEquals(PPConstants::PLATFORM_SANDBOX_ENDPOINT . "$serviceName/$apiMethod", $httpConfig->getUrl());

        $options = $this->options;
        $options['config']['mode'] = 'live';
        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), 'NV'),
            $options
        );
        $this->assertEquals(PPConstants::PLATFORM_LIVE_ENDPOINT . "$serviceName/$apiMethod", $httpConfig->getUrl());

        $customEndpoint = 'http://myhost/';
        $options = $this->options;
        $options['config']['service.EndPoint'] = $customEndpoint;
        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), 'NV'),
            $options
        );
        $this->assertEquals("$customEndpoint$serviceName/$apiMethod", $httpConfig->getUrl(), "Custom endpoint not processed");

    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testInvalidConfigurations()
    {
        $httpConfig = new PPHttpConfig();
        $handler = new PPPlatformServiceHandler(null, 'sdkname', 'sdkversion');

        $this->expectException(PayPal\Exception\PPMissingCredentialException::class);
        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), 'NV'),
            ['config' => []]
        );

        $this->expectException(PayPal\Exception\PPConfigurationException::class);
        $options = $this->options;
        unset($options['mode']);
        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), 'NV'),
            $options
        );
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testSourceHeader()
    {
        $httpConfig = new PPHttpConfig();
        $handler = new PPPlatformServiceHandler(null, 'sdkname', 'sdkversion');
        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), 'NV'),
            $this->options
        );

        $headers = $httpConfig->getHeaders();
        $this->assertArrayHasKey('X-PAYPAL-REQUEST-SOURCE', $headers);
        $this->assertMatchesRegularExpression('/.*sdkname.*/', $headers['X-PAYPAL-REQUEST-SOURCE']);
        $this->assertMatchesRegularExpression('/.*sdkversion.*/', $headers['X-PAYPAL-REQUEST-SOURCE']);
    }
}
