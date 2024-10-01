<?php

use PayPal\Core\PPConfigManager;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPConfigManager.
 *
 */
class PPConfigManagerTest extends TestCase
{
    /**
     * @var PPConfigManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->object = PPConfigManager::getInstance();
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
        $this->assertTrue($instance instanceof PPConfigManager);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGet()
    {
        $ret = $this->object->get('acct1');
        $this->assertContains('jb-us-seller_api1.paypal.com', $ret);
        $this->assertArrayHasKey('acct1.UserName', $ret);
        $this->assertTrue(sizeof($ret) == 7);

        $ret = $this->object->get('acct1.UserName');
        $this->assertEquals('jb-us-seller_api1.paypal.com', $ret);

        $ret = $this->object->get("acct");
        $this->assertEquals(sizeof($ret), 10);

    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetIniPrefix()
    {
        $ret = $this->object->getIniPrefix();
        $this->assertContains('acct1', $ret);
        $this->assertEquals(sizeof($ret), 2);

        $ret = $this->object->getIniPrefix('jb-us-seller_api1.paypal.com');
        $this->assertEquals('acct1', $ret);
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testMergeWithDefaults()
    {
        // Test file based config params and defaults
        $config = PPConfigManager::getInstance()->getConfigWithDefaults([]);
        $this->assertArrayHasKey('mode', $config, 'file config not read when no custom config is passed');
        $this->assertEquals('sandbox', $config['mode']);
        $this->assertEquals(60, $config['http.ConnectionTimeOut']);
        $this->assertEquals(120, $config['http.TimeOut']);

        // Test custom config params and defaults
        $config = PPConfigManager::getInstance()->getConfigWithDefaults(['mode' => 'custom']);
        $this->assertArrayHasKey('mode', $config);
        $this->assertEquals('custom', $config['mode']);
        $this->assertEquals(30, $config['http.ConnectionTimeOut']);
        $this->assertEquals(60, $config['http.TimeOut']);

        // Test override for default connection params
        $config = PPConfigManager::getInstance()->getConfigWithDefaults(
            ['mode' => 'custom', 'http.ConnectionTimeOut' => 100, 'http.TimeOut' => 200]
        );
        $this->assertArrayHasKey('mode', $config);
        $this->assertEquals('custom', $config['mode']);
        $this->assertEquals(100, $config['http.ConnectionTimeOut']);
        $this->assertEquals(200, $config['http.TimeOut']);
    }
}
