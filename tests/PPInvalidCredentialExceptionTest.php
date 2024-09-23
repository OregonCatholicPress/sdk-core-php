<?php

use PayPal\Exception\PPInvalidCredentialException;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPInvalidCredentialException.
 *
 */
class PPInvalidCredentialExceptionTest extends TestCase
{
    /**
     * @var PPInvalidCredentialException
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->object = new PPInvalidCredentialException();
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
    public function testErrorMessage()
    {
        $msg = $this->object->errorMessage();
        $this->assertStringContainsString('Error on line', $msg);
    }
}
