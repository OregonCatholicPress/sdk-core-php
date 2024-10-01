<?php

namespace PayPal\Exception;

use Exception;

class PPConnectionException extends Exception
{
    /**
     * Any response data that was returned by the server
     *
     * @var string
     */
    private $data;

    /**
     * @param string $url
     * @param mixed  $message
     * @param mixed  $code
     */
    public function __construct(/**
     * The url that was being connected to when the exception occured
     */
        private $url,
        $message,
        $code = 0
    ) {
        parent::__construct($message, $code);
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
