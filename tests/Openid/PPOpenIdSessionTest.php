<?php

use PayPal\Auth\Openid\PPOpenIdSession;
use PayPal\Common\PPApiContext;
use PHPUnit\Framework\TestCase;

/**
 * Test class for PPOpenIdSession.
 *
 */
class PPOpenIdSessionTest extends TestCase
{
    private $context;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    #[Override]
    protected function setUp(): void
    {
        $this->context = new PPApiContext(
            ['acct1.ClientId' => 'DummyId', 'acct1.ClientSecret' => 'A8VERY8SECRET8VALUE0', 'mode' => 'live']
        );
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
    public function testLoginUrlForMultipleScopes()
    {

        $clientId = "AQkquBDf1zctJOWGKWUEtKXm6qVhueUEMvXO_-MCI4DQQ4-LWvkDLIN2fGsd";
        $redirectUri = 'https://devtools-paypal.com/';
        $scope = ['this', 'that', 'and more'];

        $expectedBaseUrl = "https://www.sandbox.paypal.com/signin/authorize";

        $this->assertEquals(
            $expectedBaseUrl . "?client_id=$clientId&response_type=code&scope=this+that+and+more+openid&redirect_uri=" . urlencode($redirectUri),
            PPOpenIdSession::getAuthorizationUrl($redirectUri, $scope, $clientId),
            "Failed case - custom scope"
        );

        $scope = [];
        $this->assertEquals(
            $expectedBaseUrl . "?client_id=$clientId&response_type=code&scope=openid+profile+address+email+phone+" . urlencode("https://uri.paypal.com/services/paypalattributes") . "+" . urlencode('https://uri.paypal.com/services/expresscheckout') . "&redirect_uri=" . urlencode($redirectUri),
            PPOpenIdSession::getAuthorizationUrl($redirectUri, $scope, $clientId),
            "Failed case - default scope"
        );

        $scope = ['openid'];
        $this->assertEquals(
            $expectedBaseUrl . "?client_id=$clientId&response_type=code&scope=openid&redirect_uri=" . urlencode($redirectUri),
            PPOpenIdSession::getAuthorizationUrl($redirectUri, $scope, $clientId),
            "Failed case - openid scope"
        );
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testLoginWithCustomConfig()
    {

        $redirectUri = 'http://mywebsite.com';
        $scope = ['this', 'that', 'and more'];

        $expectedBaseUrl = "https://www.paypal.com/signin/authorize";

        $this->assertEquals(
            $expectedBaseUrl . "?client_id=DummyId&response_type=code&scope=this+that+and+more+openid&redirect_uri=" . urlencode($redirectUri),
            PPOpenIdSession::getAuthorizationUrl($redirectUri, $scope, "DummyId", null, null, $this->context),
            "Failed case - custom config"
        );
    }

    #[PHPUnit\Framework\Attributes\Test]
    public function testLogoutWithCustomConfig()
    {

        $redirectUri = 'http://mywebsite.com';
        $idToken = 'abc';

        $expectedBaseUrl = "https://www.paypal.com/webapps/auth/protocol/openidconnect/v1/endsession";

        $this->assertEquals(
            $expectedBaseUrl . "?id_token=$idToken&redirect_uri=" . urlencode($redirectUri) . "&logout=true",
            PPOpenIdSession::getLogoutUrl($redirectUri, $idToken, $this->context),
            "Failed case - custom config"
        );
    }
}
