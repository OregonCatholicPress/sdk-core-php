<?php

namespace PayPal\Auth\Oauth;

use Override;
use Stringable;

class OAuthConsumer implements Stringable
{
    public function __construct(public $key, public $secret, $callback_url = null)
    {
        $this->callback_url = $callback_url;
    }

    #[Override]
    public function __toString(): string
    {
        return "OAuthConsumer[key=$this->key,secret=$this->secret]";
    }
}
