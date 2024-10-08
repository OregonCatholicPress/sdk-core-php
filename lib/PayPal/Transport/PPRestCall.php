<?php

namespace PayPal\Transport;

use PayPal\Core\PPHttpConfig;
use PayPal\Core\PPHttpConnection;
use PayPal\Core\PPLoggingManager;

class PPRestCall
{
    /**
     *
     * @var PPLoggingManager logger interface
     */
    private $logger;

    public function __construct(private $apiContext)
    {
        $this->logger     = new PPLoggingManager(self::class, $this->apiContext->getConfig());
    }

    /**
     * @param array  $handlers array of handlers
     * @param string $path     Resource path relative to base service endpoint
     * @param string $method   HTTP method - one of GET, POST, PUT, DELETE, PATCH etc
     * @param string $data     Request payload
     * @param array  $headers  HTTP headers
     */
    public function execute($handlers, $path, $method, $data = '', $headers = [])
    {

        $config     = $this->apiContext->getConfig();
        $httpConfig = new PPHttpConfig(null, $method);
        $httpConfig->setHeaders(
            $headers +
          ['Content-Type' => 'application/json']
        );

        foreach ($handlers as $handler) {
            if (!is_object($handler)) {
                $shandler = "\\" . $handler;
                $handler  = new $shandler($this->apiContext);
            }
            $handler->handle($httpConfig, $data, ['path' => $path, 'apiContext' => $this->apiContext]);
        }
        $connection = new PPHttpConnection($httpConfig, $config);
        $response   = $connection->execute($data);
        $this->logger->fine($response);

        return $response;
    }
}
