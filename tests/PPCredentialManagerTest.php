<?php

use PayPal\Core\PPCredentialManager;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPCredentialManager.
 *
 */
class PPCredentialManagerTest extends TestCase
{
    /**
     * @var PPCredentialManager
     */
    protected $object;

    private $config = ['acct1.UserName' => 		'jb-us-seller_api1.paypal.com', 'acct1.Password' => 		'WX4WTU3S8MY44S7F', 'acct1.Signature' => 		'AFcWxV21C7fd0v3bYYYRCpSSRl31A7yDhhsPUU2XhtMoZXsWHFxu-RWy', 'acct1.AppId' => 			'APP-80W284485P519543T', 'acct1.Subject' => 			'email@paypal.com', 'acct2.UserName' => 		'certuser_biz_api1.paypal.com', 'acct2.Password' => 		'D6JNKKULHN3G5B8A', 'acct2.CertPath' => 		'cert_key.pem', 'acct2.AppId' => 			'APP-80W284485P519543T', 'acct3.ClientId' => 		'client-id', 'acct3.ClientSecret' => 	'client-secret', 'http.ConnectionTimeOut' => '30', 'http.TimeOut' => '60', 'http.Retry' => 			'5', 'service.RedirectURL' => 	'https://www.sandbox.paypal.com/webscr&cmd=', 'service.DevCentralURL' => 	'https://developer.paypal.com', 'service.EndPoint.IPN' => 	'https://www.sandbox.paypal.com/cgi-bin/webscr', 'service.EndPoint.AdaptivePayments' => 'https://svcs.sandbox.paypal.com/', 'service.SandboxEmailAddress' => 'platform_sdk_seller@gmail.com', 'log.FileName' => 			'PayPal.log', 'log.LogLevel' => 			'INFO', 'log.LogEnabled' => 		'1'];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->object = PPCredentialManager::getInstance($this->config);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    #[Override]
    protected function tearDown(): void
    {
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetInstance()
    {
        $instance = $this->object->getInstance($this->config);
        $this->assertTrue($instance instanceof PPCredentialManager);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetSpecificCredentialObject()
    {
        $cred = $this->object->getCredentialObject('jb-us-seller_api1.paypal.com');
        $this->assertNotNull($cred);
        $this->assertEquals('jb-us-seller_api1.paypal.com', $cred->getUsername());

        $cred = $this->object->getCredentialObject('certuser_biz_api1.paypal.com');
        $this->assertNotNull($cred);
        $this->assertEquals('certuser_biz_api1.paypal.com', $cred->getUsername());
        $this->assertStringEndsWith('cert_key.pem', $cred->getCertificatePath());
    }

    #[PHPUnit\Framework\Attributes\Test]
    #[PHPUnit\Framework\Attributes\Group('defective')]
    public function testGetInvalidCredentialObject()
    {
        $this->expectException(PayPal\Exception\PPInvalidCredentialException::class);
        $cred = $this->object->getCredentialObject('invalid_biz_api1.gmail.com');
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetDefaultCredentialObject()
    {
        $cred = $this->object->getCredentialObject();
        $this->assertEquals('jb-us-seller_api1.paypal.com', $cred->getUsername());

        // Test to see if default account for REST credentials works
        // as expected
        $o = PPCredentialManager::getInstance(['mode' => 'sandbox', 'acct1.ClientId' => 		'client-id', 'acct1.ClientSecret' => 	'client-secret', 'acct2.UserName' => 		'certuser_biz_api1.paypal.com', 'acct2.Password' => 		'D6JNKKULHN3G5B8A', 'acct2.CertPath' => 		'cert_key.pem', 'acct2.AppId' => 		'APP-80W284485P519543T']);
        $cred = $o->getCredentialObject();
        $this->assertEquals('client-id', $cred['clientId']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetPlatformCredentialObject()
    {
        $cred = $this->object->getCredentialObject();
        $this->assertEquals('APP-80W284485P519543T', $cred->getApplicationId());
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetSubjectCredentialObject()
    {
        $cred = $this->object->getCredentialObject('jb-us-seller_api1.paypal.com');

        $this->assertNotNull($cred);
        $this->assertNotNull($cred->getThirdPartyAuthorization());
        $this->assertEquals(PayPal\Auth\PPSubjectAuthorization::class, $cred->getThirdPartyAuthorization()::class);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetRestCredentialObject()
    {
        $cred = $this->object->getCredentialObject('acct3');

        $this->assertNotNull($cred);

        $this->assertArrayHasKey('clientId', $cred);
        $this->assertEquals($this->config['acct3.ClientId'], $cred['clientId']);

        $this->assertArrayHasKey('clientSecret', $cred);
        $this->assertEquals($this->config['acct3.ClientSecret'], $cred['clientSecret']);
    }

    #[PHPUnit\Framework\Attributes\Test]
    #[PHPUnit\Framework\Attributes\Group('defective')]
    public function testInvalidConfiguration()
    {
        $this->expectException(PayPal\Exception\PPMissingCredentialException::class);
        $o = PPCredentialManager::getInstance(['mode' => 'sandbox']);
    }
}
