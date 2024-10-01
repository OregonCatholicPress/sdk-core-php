<?php

use PayPal\Auth\PPSignatureCredential;
use PayPal\Auth\PPSubjectAuthorization;
use PayPal\Auth\PPTokenAuthorization;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPSignatureCredential.
 *
 */
class PPSignatureCredentialTest extends TestCase
{
    /**
     * @var PPSignatureCredential
     */
    protected $merchantCredential;

    protected $platformCredential;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->merchantCredential = new PPSignatureCredential("platfo_1255077030_biz_api1.gmail.com", "1255077037", "Abg0gYcQyxQvnf2HDJkKtA-p6pqhA1k-KTYE0Gcy1diujFio4io5Vqjf");

        $this->platformCredential = new PPSignatureCredential("platfo_1255077030_biz_api1.gmail.com", "1255077037", "Abg0gYcQyxQvnf2HDJkKtA-p6pqhA1k-KTYE0Gcy1diujFio4io5Vqjf");
        $this->platformCredential->setApplicationId("APP-80W284485P519543T");
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
    public function testValidateUsername()
    {
        $this->expectException(PayPal\Exception\PPMissingCredentialException::class);
        $cred = new PPSignatureCredential("", "1255077037", "Abg0gYcQyxQvnf2HDJkKtA-p6pqhA1k-KTYE0Gcy1diujFio4io5Vqjf");
        $cred->validate();
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testValidatepwd()
    {
        $this->expectException(PayPal\Exception\PPMissingCredentialException::class);
        $cred = new PPSignatureCredential("platfo_1255077030_biz_api1.gmail.com", "", "Abg0gYcQyxQvnf2HDJkKtA-p6pqhA1k-KTYE0Gcy1diujFio4io5Vqjf");
        $cred->validate();
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetSignature()
    {
        $this->assertEquals('Abg0gYcQyxQvnf2HDJkKtA-p6pqhA1k-KTYE0Gcy1diujFio4io5Vqjf', $this->merchantCredential->getSignature());
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetUserName()
    {
        $this->assertEquals('platfo_1255077030_biz_api1.gmail.com', $this->merchantCredential->getUserName());
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetPassword()
    {
        $this->assertEquals('1255077037', $this->merchantCredential->getPassword());
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testGetAppId()
    {
        $this->assertEquals('APP-80W284485P519543T', $this->platformCredential->getApplicationId());
    }

    public function testThirdPartyAuthorization()
    {
        $authorizerEmail = "merchant@domain.com";
        $thirdPartyAuth = new PPSubjectAuthorization($authorizerEmail);
        $cred = new PPSignatureCredential("username", "pwd", "signature");
        $cred->setThirdPartyAuthorization($thirdPartyAuth);
        $this->assertEquals($cred->getThirdPartyAuthorization()->getSubject(), $authorizerEmail);

        $accessToken = "atoken";
        $tokenSecret = "asecret";
        $thirdPartyAuth = new PPTokenAuthorization($accessToken, $tokenSecret);
        $cred->setThirdPartyAuthorization($thirdPartyAuth);
        $this->assertEquals($cred->getThirdPartyAuthorization()->getAccessToken(), $accessToken);
        $this->assertEquals($cred->getThirdPartyAuthorization()->getTokenSecret(), $tokenSecret);
    }
}
