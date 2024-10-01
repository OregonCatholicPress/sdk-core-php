<?php

use PayPal\Auth\PPSignatureCredential;
use PayPal\Core\PPConstants;
use PayPal\Core\PPHttpConfig;
use PayPal\Core\PPRequest;
use PayPal\Handler\PPMerchantServiceHandler;
use PHPUnit\Framework\TestCase;

class PPMerchantServiceHandlerTest extends TestCase
{
    private $options;

    #[Override]
    protected function setUp(): void
    {
        $this->options = ['config' => ['mode' => 'sandbox', 'acct1.UserName' => 'siguser', 'acct1.Password' => 'pass', 'acct1.Signature' => 'signature', 'acct2.UserName' => 'certuser', 'acct2.Password' => 'pass', 'acct2.CertPath' => 'pathtocert'], 'serviceName' => 'PayPalAPIInterfaceService', 'apiMethod' => 'DoExpressCheckout', 'port' => 'apiAA'];
    }

    #[Override]
    protected function tearDown(): void
    {

    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testHeadersAdded()
    {

        $req = new PPRequest(new stdClass(), 'SOAP');

        $httpConfig = new PPHttpConfig();
        $handler = new PPMerchantServiceHandler(null, 'sdkname', 'sdkversion');
        $handler->handle($httpConfig, $req, $this->options);

        $this->assertEquals(5, count($httpConfig->getHeaders()), "Basic headers not added");

    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testModeBasedEndpointForSignatureCredential()
    {

        $httpConfig = new PPHttpConfig();
        $handler = new PPMerchantServiceHandler(null, 'sdkname', 'sdkversion');
        $req = new PPRequest(new stdClass(), 'SOAP');
        $req->setCredential(new PPSignatureCredential('a', 'b', 'c'));

        $handler->handle($httpConfig, $req, $this->options);
        $this->assertEquals(PPConstants::MERCHANT_SANDBOX_SIGNATURE_ENDPOINT, $httpConfig->getUrl());

        $options = $this->options;
        $options['config']['mode'] = 'live';
        $handler->handle($httpConfig, $req, $options);
        $this->assertEquals(PPConstants::MERCHANT_LIVE_SIGNATURE_ENDPOINT, $httpConfig->getUrl());

    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testModeBasedEndpointForCertificateCredential()
    {

        $httpConfig = new PPHttpConfig();
        $handler = new PPMerchantServiceHandler('certuser', 'sdkname', 'sdkversion');
        $req = new PPRequest(new stdClass(), 'SOAP');

        $handler->handle($httpConfig, $req, $this->options);
        $this->assertEquals(PPConstants::MERCHANT_SANDBOX_CERT_ENDPOINT, $httpConfig->getUrl());

        $options = $this->options;
        $options['config']['mode'] = 'live';
        $handler->handle($httpConfig, $req, $options);
        $this->assertEquals(PPConstants::MERCHANT_LIVE_CERT_ENDPOINT, $httpConfig->getUrl());

    }

    public function testCustomEndpoint()
    {

        $customEndpoint = 'http://myhost/';
        $options = $this->options;
        $options['config']['service.EndPoint'] = $customEndpoint;

        $httpConfig = new PPHttpConfig();
        $handler = new PPMerchantServiceHandler(null, 'sdkname', 'sdkversion');

        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), 'SOAP'),
            $options
        );
        $this->assertEquals("$customEndpoint", $httpConfig->getUrl(), "Custom endpoint not processed");

        $options['config']['service.EndPoint'] = 'abc';
        $options['config']["service.EndPoint." . $options['port']] = $customEndpoint;
        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), 'SOAP'),
            $options
        );
        $this->assertEquals("$customEndpoint", $httpConfig->getUrl(), "Custom endpoint not processed");

    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testInvalidConfigurations()
    {
        $httpConfig = new PPHttpConfig();
        $handler = new PPMerchantServiceHandler(null, 'sdkname', 'sdkversion');

        $this->expectException(PayPal\Exception\PPMissingCredentialException::class);
        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), 'SOAP'),
            ['config' => []]
        );
        $this->expectException(PayPal\Exception\PPConfigurationException::class);

        $options = $this->options;
        unset($options['mode']);
        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), 'SOAP'),
            $options
        );
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testSourceHeader()
    {
        $httpConfig = new PPHttpConfig();
        $handler = new PPMerchantServiceHandler(null, 'sdkname', 'sdkversion');
        $handler->handle(
            $httpConfig,
            new PPRequest(new stdClass(), 'SOAP'),
            $this->options
        );

        $headers = $httpConfig->getHeaders();
        $this->assertArrayHasKey('X-PAYPAL-REQUEST-SOURCE', $headers);
        $this->assertMatchesRegularExpression('/.*sdkname.*/', $headers['X-PAYPAL-REQUEST-SOURCE']);
        $this->assertMatchesRegularExpression('/.*sdkversion.*/', $headers['X-PAYPAL-REQUEST-SOURCE']);
    }
}
