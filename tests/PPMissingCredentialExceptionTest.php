<?php

use PayPal\Exception\PPMissingCredentialException;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPMissingCredentialException.
 *
 */
class PPMissingCredentialExceptionTest extends TestCase
{
    /**
     * @var PPMissingCredentialException
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->object = new PPMissingCredentialException();
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
