<?php

namespace PayPal\Auth\Oauth;

use Override;
use Stringable;

class OAuthToken implements Stringable
{
    /**
     * key = the token
     * secret = the token secret
     *
     * @param mixed $key
     * @param mixed $secret
     */
    public function __construct(public $key, public $secret)
    {
    }

    /**
     * generates the basic string serialization of a token that a server
     * would respond to request_token and access_token calls with
     */
    public function to_string()
    {
        return "oauth_token=" .
        OAuthUtil::urlencode_rfc3986($this->key) .
        "&oauth_token_secret=" .
        OAuthUtil::urlencode_rfc3986($this->secret);
    }

    #[Override]
    public function __toString(): string
    {
        return (string) $this->to_string();
    }
}
