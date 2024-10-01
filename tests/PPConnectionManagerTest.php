<?php

use PayPal\Core\PPConnectionManager;
use PayPal\Core\PPHttpConfig;
use PayPal\Core\PPHttpConnection;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPConnectionManager.
 *
 */
class PPConnectionManagerTest extends TestCase
{
    /**
     * @var PPConnectionManager
     */
    protected $object;

    private $config = ['acct1.UserName' => 'jb-us-seller_api1.paypal.com', 'acct1.Password' => 'WX4WTU3S8MY44S7F', 'acct1.Signature' => 	'AFcWxV21C7fd0v3bYYYRCpSSRl31A7yDhhsPUU2XhtMoZXsWHFxu-RWy', 'acct1.AppId' => 	'APP-80W284485P519543T', 'acct2.UserName' => 	'certuser_biz_api1.paypal.com', 'acct2.Password' => 	'D6JNKKULHN3G5B8A', 'acct2.CertPath' => 	'cert_key.pem', 'acct2.AppId' => 	'APP-80W284485P519543T', 'http.ConnectionTimeOut' => 	'30', 'http.TimeOut' => 	'60', 'http.Retry' => 	'5', 'service.RedirectURL' => 	'https://www.sandbox.paypal.com/webscr&cmd=', 'service.DevCentralURL' => 'https://developer.paypal.com', 'service.EndPoint.IPN' => 'https://www.sandbox.paypal.com/cgi-bin/webscr', 'service.EndPoint.AdaptivePayments' => 'https://svcs.sandbox.paypal.com/', 'service.SandboxEmailAddress' => 'platform_sdk_seller@gmail.com', 'log.FileName' => 'PayPal.log', 'log.LogLevel' => 	'INFO', 'log.LogEnabled' => 	'1'];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->object =  PPConnectionManager::getInstance();
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
        $instance = $this->object->getInstance();
        $this->assertTrue($instance instanceof PPConnectionManager);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetConnection()
    {
        $conn = $this->object->getConnection(new PPHttpConfig("http://domain.com"), $this->config);
        $this->assertNotNull($conn);
        $this->assertTrue($conn instanceof PPHttpConnection);
        $this->assertEquals(PPHttpConnection::class, $conn::class);
    }
}
