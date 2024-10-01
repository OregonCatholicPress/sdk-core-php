<?php

use PayPal\Auth\PPCertificateCredential;
use PayPal\Exception\PPMissingCredentialException;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPCertificateCredential.
 *
 */
class PPCertificateCredentialTest extends TestCase
{
    /**
     * @var PPCertificateCredential
     */
    protected $credential;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->credential = new PPCertificateCredential("platfo_1255077030_biz_api1.gmail.com", "1255077037", "cacert.pem");
        $this->credential->setApplicationId('APP-80W284485P519543T');
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
    #[PHPUnit\Framework\Attributes\Group('defective')]
    public function testValidateUname()
    {
        $this->expectException(PPMissingCredentialException::class);
        $credUname = new PPCertificateCredential("", "1255077037", "cacert.pem");
        $credUname->validate();
        $setNotExpectedException('PPMissingCredentialException');
        $credCorrect = new PPCertificateCredential("platfo_1255077030_biz_api1.gmail.com", "1255077037", "cacert.pem");
        $var = $credCorrect->validate();
        $this->assertNull($var);
    }

    #[PHPUnit\Framework\Attributes\Test]
    #[PHPUnit\Framework\Attributes\Group('defective')]
    public function testValidatePwd()
    {
        $this->expectException(PPMissingCredentialException::class);
        $credpwd = new PPCertificateCredential("platfo_1255077030_biz_api1.gmail.com", "", "cacert.pem");
        $credpwd->validate();

    }

    #[PHPUnit\Framework\Attributes\Test]
    #[PHPUnit\Framework\Attributes\Group('defective')]
    public function testValidateCertPath()
    {
        $this->expectException(PPMissingCredentialException::class);
        $credCertPath = new PPCertificateCredential("platfo_1255077030_biz_api1.gmail.com", "1255077037", "");
        $credCertPath->validate();
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetAppId()
    {
        $credAppid = new PPCertificateCredential("platfo_1255077030_biz_api1.gmail.com", "1255077037", "cacert.pem");
        $credAppid->setApplicationId("APP-ID");
        $this->assertEquals($credAppid->getApplicationId(), "APP-ID");
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetUserName()
    {
        $this->assertEquals('platfo_1255077030_biz_api1.gmail.com', $this->credential->getUserName());

    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetPassword()
    {
        $this->assertEquals('1255077037', $this->credential->getPassword());
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetCertificatePath()
    {
        $this->assertStringEndsWith(__DIR__ . DIRECTORY_SEPARATOR . 'cacert.pem', $this->credential->getCertificatePath());
    }

    /*@test
     */
    public function testGetApplicationId()
    {
        $this->assertEquals('APP-80W284485P519543T', $this->credential->getApplicationId());
    }
}
