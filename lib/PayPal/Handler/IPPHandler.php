<?php

namespace PayPal\Handler;

use PayPal\Core\PPHttpConfig;
use PayPal\Core\PPRequest;

interface IPPHandler
{
    /**
     *
     * @param PPHttpConfig $httpConfig
     * @param PPRequest    $request
     * @param mixed        $options
     */
    public function handle($httpConfig, $request, $options);
}
