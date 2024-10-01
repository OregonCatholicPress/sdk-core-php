<?php

use PayPal\Auth\PPCertificateCredential;
use PayPal\Auth\PPSubjectAuthorization;
use PayPal\Auth\PPTokenAuthorization;
use PayPal\Core\PPHttpConfig;
use PayPal\Core\PPRequest;
use PayPal\Handler\PPCertificateAuthHandler;
use PHPUnit\Framework\TestCase;

class PPCertificateAuthHandlerTest extends TestCase
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
    public function testHeadersAddedForNVP()
    {

        $req = new PPRequest(new stdClass(), 'NV');
        $options = ['config' => ['mode' => 'sandbox'], 'serviceName' => 'AdaptivePayments', 'apiMethod' => 'ConvertCurrency'];

        $handler = new PPCertificateAuthHandler();

        // Test that no headers are added if no credential is passed
        $httpConfig = new PPHttpConfig();
        $handler->handle($httpConfig, $req, $options);
        $this->assertEquals(0, count($httpConfig->getHeaders()));

        // Test that the 3 token headers are added for first party API calls
        $httpConfig = new PPHttpConfig();
        $cred = new PPCertificateCredential('user', 'pass', 'cacert.pem');
        $req->setCredential($cred);

        $handler->handle($httpConfig, $req, $options);
        $this->assertEquals(2, count($httpConfig->getHeaders()));
        $this->assertArrayHasKey(CURLOPT_SSLCERT, $httpConfig->getCurlOptions());

        // Test addition of 'subject' HTTP header for subject based third party auth
        $httpConfig = new PPHttpConfig();
        $cred = new PPCertificateCredential('user', 'pass', 'cacert.pem');
        $cred->setThirdPartyAuthorization(new PPSubjectAuthorization('email@paypal.com'));
        $req->setCredential($cred);

        $handler->handle($httpConfig, $req, $options);
        $this->assertEquals(3, count($httpConfig->getHeaders()));
        $this->assertArrayHasKey('X-PAYPAL-SECURITY-SUBJECT', $httpConfig->getHeaders());
        $this->assertArrayHasKey(CURLOPT_SSLCERT, $httpConfig->getCurlOptions());

        // Test that no auth related HTTP headers (username, password, sign?) are
        // added for token based third party auth
        $httpConfig = new PPHttpConfig();
        $req->getCredential()->setThirdPartyAuthorization(new PPTokenAuthorization('token', 'tokenSecret'));

        $handler->handle($httpConfig, $req, $options);
        $this->assertEquals(0, count($httpConfig->getHeaders()));
        $this->assertArrayHasKey(CURLOPT_SSLCERT, $httpConfig->getCurlOptions());

    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testHeadersAddedForSOAP()
    {

        $options = ['config' => ['mode' => 'sandbox'], 'serviceName' => 'AdaptivePayments', 'apiMethod' => 'ConvertCurrency'];
        $req = new PPRequest(new stdClass(), 'SOAP');

        $handler = new PPCertificateAuthHandler();

        // Test that no headers are added if no credential is passed
        $httpConfig = new PPHttpConfig();
        $handler->handle($httpConfig, $req, $options);
        $this->assertEquals('', $req->getBindingInfo('securityHeader'));

        // Test that the 3 token SOAP headers are added for first party API calls
        $req = new PPRequest(new stdClass(), 'SOAP');
        $req->setCredential(new PPCertificateCredential('user', 'pass', 'cacert.pem'));
        $handler->handle($httpConfig, $req, $options);

        $this->assertStringContainsString('<ebl:Username>', $req->getBindingInfo('securityHeader'));
        $this->assertStringContainsString('<ebl:Password>', $req->getBindingInfo('securityHeader'));
        $this->assertArrayHasKey(CURLOPT_SSLCERT, $httpConfig->getCurlOptions());

        // Test addition of 'subject' SOAP header for subject based third party auth
        $req = new PPRequest(new stdClass(), 'SOAP');
        $cred = new PPCertificateCredential('user', 'pass', 'cacert.pem');
        $cred->setThirdPartyAuthorization(new PPSubjectAuthorization('email@paypal.com'));
        $req->setCredential($cred);
        $handler->handle($httpConfig, $req, $options);

        $this->assertStringContainsString('<ebl:Username>', $req->getBindingInfo('securityHeader'));
        $this->assertStringContainsString('<ebl:Password>', $req->getBindingInfo('securityHeader'));
        $this->assertStringContainsString('<ebl:Subject>', $req->getBindingInfo('securityHeader'));
        $this->assertArrayHasKey(CURLOPT_SSLCERT, $httpConfig->getCurlOptions());

        // Test that no auth related HTTP headers (username, password, sign?) are
        // added for token based third party auth
        $req = new PPRequest(new stdClass(), 'SOAP');
        $req->setCredential(new PPCertificateCredential('user', 'pass', 'cacert.pem'));
        $req->getCredential()->setThirdPartyAuthorization(new PPTokenAuthorization('token', 'tokenSecret'));
        $handler->handle($httpConfig, $req, $options);

        $this->assertStringContainsString('<ns:RequesterCredentials/>', $req->getBindingInfo('securityHeader'));
        $this->assertEquals(0, count($httpConfig->getHeaders()));
        $this->assertArrayHasKey(CURLOPT_SSLCERT, $httpConfig->getCurlOptions());
    }
}
