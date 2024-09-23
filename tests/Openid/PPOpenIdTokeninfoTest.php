<?php

use PayPal\Auth\Openid\PPOpenIdTokeninfo;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPOpenIdTokeninfo.
 *
 */
class PPOpenIdTokeninfoTest extends TestCase
{
    public $token;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->token = new PPOpenIdTokeninfo();
        $this->token->setAccessToken("Access token")
            ->setExpiresIn(900)
            ->setRefreshToken("Refresh token")
            ->setIdToken("id token")
            ->setScope("openid address")
            ->setTokenType("Bearer");
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
        $tokenCopy = new PPOpenIdTokeninfo();
        $tokenCopy->fromJson($this->token->toJson());

        $this->assertEquals($this->token, $tokenCopy);
    }

    /**
     * @t1est
     */
    public function t1estOperations()
    {

        $clientId = 'AQkquBDf1zctJOWGKWUEtKXm6qVhueUEMvXO_-MCI4DQQ4-LWvkDLIN2fGsd';
        $clientSecret = 'ELtVxAjhT7cJimnz5-Nsx9k2reTKSVfErNQF-CmrwJgxRtylkGTKlU4RvrX';

        $params = ['code' => '<FILLME>', 'redirect_uri' => 'https://devtools-paypal.com/', 'client_id' => $clientId, 'client_secret' => $clientSecret];
        $accessToken = PPOpenIdTokeninfo::createFromAuthorizationCode($params);
        $this->assertNotNull($accessToken);

        $params = ['refresh_token' => $accessToken->getRefreshToken(), 'client_id' => $clientId, 'client_secret' => $clientSecret];
        $accessToken = $accessToken->createFromRefreshToken($params);
        $this->assertNotNull($accessToken);
    }
}
