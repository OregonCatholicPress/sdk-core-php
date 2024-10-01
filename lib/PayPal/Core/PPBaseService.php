<?php

namespace PayPal\Core;

use PayPal\Common\PPApiContext;

class PPBaseService
{
    protected $lastRequest;
    protected $lastResponse;

    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    public function getServiceName()
    {
        return $this->serviceName;
    }

    public function __construct(private $serviceName, private $serviceBinding, protected $config = null, private $handlers = [])
    {
    }

    /**
     *
     * @param string     $method        - API method to call
     * @param object     $requestObject Request object
     * @param apiContext $apiContext    object containing credential and SOAP headers
     * @param array      $handlers      Array of Handlers
     * @param mixed      $apiUserName   - Optional API credential - can either be
     *                                  a username configured in sdk_config.ini or a ICredential object created dynamically
     * @param mixed      $port
     */
    public function call($port, $method, $requestObject, $apiContext, $handlers = [])
    {

        if (!is_array($handlers)) {
            $handlers = [];
        }

        if (is_array($this->handlers)) {
            $handlers = array_merge($this->handlers, $handlers);
        }

        if ($apiContext == null) {
            $apiContext = new PPApiContext(PPConfigManager::getConfigWithDefaults($this->config));
        }
        if ($apiContext->getConfig() == null) {
            $apiContext->setConfig(PPConfigManager::getConfigWithDefaults($this->config));
        }

        $service            = new PPAPIService(
            $port,
            $this->serviceName,
            $this->serviceBinding,
            $apiContext,
            $handlers
        );
        $ret                = $service->makeRequest($method, new PPRequest($requestObject, $this->serviceBinding));
        $this->lastRequest  = $ret['request'];
        $this->lastResponse = $ret['response'];

        return $this->lastResponse;
    }
}
