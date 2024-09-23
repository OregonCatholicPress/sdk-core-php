<?php

use PayPal\Core\PPLoggingManager;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPLoggingManager.
 *
 */
class PPLoggingManagerTest extends TestCase
{
    /**
     * @var PPLoggingManager
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->object = new PPLoggingManager('InvoiceTest');
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
    #[PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testError()
    {
        $this->object->error('Test Error Message');

    }

    #[PHPUnit\Framework\Attributes\Test]
    #[PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testWarning()
    {
        $this->object->warning('Test Warning Message');
    }

    #[PHPUnit\Framework\Attributes\Test]
    #[PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testInfo()
    {
        $this->object->info('Test info Message');
    }

    #[PHPUnit\Framework\Attributes\Test]
    #[PHPUnit\Framework\Attributes\DoesNotPerformAssertions]
    public function testFine()
    {
        $this->object->fine('Test fine Message');
    }
}
