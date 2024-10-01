<?php

use PayPal\Auth\Openid\PPOpenIdError;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPOpenIdError.
 *
 */
class PPOpenIdErrorTest extends TestCase
{
    private $error;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->error = new PPOpenIdError();
        $this->error->setErrorDescription('error description')
            ->setErrorUri('http://developer.paypal.com/api/error')
            ->setError('VALIDATION_ERROR');
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
    public function testSerializationDeserialization()
    {
        $errorCopy = new PPOpenIdError();
        $errorCopy->fromJson($this->error->toJson());

        $this->assertEquals($this->error, $errorCopy);
    }
}
