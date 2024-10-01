<?php

namespace PayPal\Core;

use PayPal\Auth\IPPCredential;

/**
 * Encapsulates API request information
 *
 */
class PPRequest
{
    /**
     * Optional credentials associated with
     * the request
     *
     * @var IPPCredential
     */
    private $credential;

    /**
     *
     * Holder for any binding specific info
     *
     * @var array
     */
    private $bindingInfo = [];

    /**
     * @param object $requestObject
     * @param string $bindingType
     */
    public function __construct(
        /**
         * Request Object
         */
        private $requestObject,
        /**
         * Transport binding for this request.
         * Can be NVP, SOAP etc
         */
        private $bindingType
    ) {
    }

    public function getRequestObject()
    {
        return $this->requestObject;
    }

    public function getBindingType()
    {
        return $this->bindingType;
    }

    public function getBindingInfo($name = null)
    {
        if (isset($name)) {
            return array_key_exists($name, $this->bindingInfo) ? $this->bindingInfo[$name] : null;
        }

        return $this->bindingInfo;
    }

    /**
     *
     * @param string $name
     */
    public function addBindingInfo($name, mixed $value)
    {
        $this->bindingInfo[$name] = $value;
    }

    public function setCredential($credential)
    {
        $this->credential = $credential;
    }

    public function getCredential()
    {
        return $this->credential;
    }
}
