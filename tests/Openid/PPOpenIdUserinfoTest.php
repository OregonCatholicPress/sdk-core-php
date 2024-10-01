<?php

use PayPal\Auth\Openid\PPOpenIdUserinfo;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPOpenIdUserinfo.
 *
 */
class PPOpenIdUserinfoTest extends TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    #[Override]
    protected function tearDown(): void
    {
    }

    #[PHPUnit\Framework\Attributes\Group('defective')]
    #[PHPUnit\Framework\Attributes\Test]
    public function testSerializationDeserialization()
    {
        $user = new PPOpenIdUserinfo();
        $user->setAccountType("PERSONAL")->setAgeRange("20-30")->setBirthday("1970-01-01")
            ->setEmail("me@email.com")->setEmailVerified(true)
            ->setFamilyName("Doe")->setMiddleName("A")->setGivenName("John")
            ->setLocale("en-US")->setGender("male")->setName("John A Doe")
            ->setPayerId("A-XZASASA")->setPhoneNumber("1-408-111-1111")
            ->setPicture("http://gravatar.com/me.jpg")
            ->setSub("me@email.com")->setUserId("userId")
            ->setVerified(true)->setVerifiedAccount(true)
            ->setZoneinfo("America/PST")->setLanguage('en_US')
            ->setAddress(PPOpenIdAddressTest::getTestData());

        $userCopy = new PPOpenIdUserinfo();
        $userCopy->fromJson($user->toJSON());

        $this->assertEquals($user, $userCopy);
    }

    #[PHPUnit\Framework\Attributes\Test]
    #[PHPUnit\Framework\Attributes\Group('defective')]
    public function testInvalidParamUserInfoCall()
    {
        $this->expectException(PayPal\Exception\PPConnectionException::class);
        PPOpenIdUserinfo::getUserinfo(['access_token' => 'accessToken']);
    }
}
