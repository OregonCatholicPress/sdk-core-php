<?php

use PayPal\Exception\PPConfigurationException;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPConfigurationException.
 *
 */
class PPConfigurationExceptionTest extends TestCase
{
    /**
     * @var PPConfigurationException
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->object = new PPConfigurationException('Test PPConfigurationException');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    #[Override]
    protected function tearDown(): void
    {
    }

    public function testPPConfigurationException()
    {
        $this->assertEquals('Test PPConfigurationException', $this->object->getMessage());
    }
}
