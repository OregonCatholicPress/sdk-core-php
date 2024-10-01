<?php

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\Call;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

class CallTest extends TestCase
{
    #[Group('defective')]
    public function testExecuteWithExplicitCredentials()
    {
        $cred = new OAuthTokenCredential(Constants::CLIENT_ID, Constants::CLIENT_SECRET);
        $data = '"request":"test message"';

        $call = new Call();
        $ret = $call->execute('/v1/payments/echo', "POST", $data, $cred);
        $this->assertEquals($data, $ret);
    }

    #[Group('defective')]
    public function testExecuteWithInvalidCredentials()
    {

        $cred = new OAuthTokenCredential('test', 'dummy');
        $data = '"request":"test message"';

        $call = new Call();
        $this->expectException('\PPConnectionException');
        $ret = $call->execute('/v1/payments/echo', "POST", $data, $cred);

    }

    #[Group('defective')]
    public function testExecuteWithDefaultCredentials()
    {

        $data = '"request":"test message"';

        $call = new Call();
        $ret = $call->execute('/v1/payments/echo', "POST", $data);
        $this->assertEquals($data, $ret);
    }
}
