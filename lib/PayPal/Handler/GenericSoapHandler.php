<?php

namespace PayPal\Handler;

use Override;

class GenericSoapHandler implements IPPHandler
{
    public function __construct(private $namespace)
    {
    }

    #[Override]
    public function handle($httpConfig, $request, $options)
    {

        if (isset($options['apiContext'])) {
            if ($options['apiContext']->getHttpHeaders() != null) {
                $httpConfig->setHeaders($options['apiContext']->getHttpHeaders());
            }
            if ($options['apiContext']->getSOAPHeader() != null) {
                $request->addBindingInfo('securityHeader', $options['apiContext']->getSOAPHeader()->toXMLString());
            }
        }

        if (isset($options['config']['service.EndPoint'])) {
            $httpConfig->setUrl($options['config']['service.EndPoint']);
        }
        if (!array_key_exists('Content-Type', $httpConfig->getHeaders())) {
            $httpConfig->addHeader('Content-Type', 'text/xml');
        }

        $request->addBindingInfo("namespace", $this->namespace);
    }
}
