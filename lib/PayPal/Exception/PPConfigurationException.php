<?php

namespace PayPal\Exception;

use Exception;

class PPConfigurationException extends Exception
{
    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
