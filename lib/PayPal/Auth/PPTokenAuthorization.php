<?php

namespace PayPal\Auth;

/**
 *
 * Represents token based third party authorization
 * Token based authorization credentials are obtained using
 * the Permissions API
 */
class PPTokenAuthorization implements IPPThirdPartyAuthorization
{
    /**
     * @param string $accessToken
     * @param string $tokenSecret
     */
    public function __construct(
        /**
         * Permanent access token that identifies the relationship
         * between the authorizing user and the API caller.
         */
        private $accessToken,
        /**
         * The token secret/password that will need to be used when
         * generating the signature.
         */
        private $tokenSecret
    ) {
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getTokenSecret()
    {
        return $this->tokenSecret;
    }
}
