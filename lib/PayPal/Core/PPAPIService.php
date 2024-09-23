<?php

namespace PayPal\Core;

use PayPal\Formatter\FormatterFactory;

class PPAPIService
{
    public $apiMethod;
    private $logger;

    public function __construct(private $port, public $serviceName, private $serviceBinding, public $apiContext, private $handlers = [])
    {

        $this->logger         = new PPLoggingManager(self::class, $this->apiContext->getConfig());
    }

    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * Register additional handlers to run before
     * executing this call
     *
     * @param IPPHandler $handler
     */
    public function addHandler($handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * Execute an api call
     *
     * @param string    $apiMethod Name of the API operation (such as 'Pay')
     * @param PPRequest $params    Request object
     * @param mixed     $request
     *
     * @return array containing request and response
     */
    public function makeRequest($apiMethod, $request)
    {

        $this->apiMethod = $apiMethod;

        $httpConfig = new PPHttpConfig(null, PPHttpConfig::HTTP_POST);
        if ($this->apiContext->getHttpHeaders() != null) {
            $httpConfig->setHeaders($this->apiContext->getHttpHeaders());
        }
        $this->runHandlers($httpConfig, $request);

        // Serialize request object to a string according to the binding configuration
        $formatter = FormatterFactory::factory($this->serviceBinding);
        $payload   = $formatter->toString($request);

        // Execute HTTP call
        $connection = PPConnectionManager::getInstance()->getConnection($httpConfig, $this->apiContext->getConfig());
        $this->logger->info("Request: $payload");
        $response = $connection->execute($payload);
        $this->logger->info("Response: $response");

        return ['request' => $payload, 'response' => $response];
    }

    private function runHandlers($httpConfig, $request)
    {

        $options = $this->getOptions();
        foreach ($this->handlers as $handlerClass) {
            $handlerClass->handle($httpConfig, $request, $options);
        }
    }

    private function getOptions()
    {
        return ['port'           => $this->port, 'serviceName'    => $this->serviceName, 'serviceBinding' => $this->serviceBinding, 'config'         => $this->apiContext->getConfig(), 'apiMethod'      => $this->apiMethod, 'apiContext'     => $this->apiContext];
    }
}
