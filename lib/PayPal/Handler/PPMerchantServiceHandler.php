<?php

namespace PayPal\Handler;

use Override;
use PayPal\Auth\PPCertificateCredential;
use PayPal\Auth\PPSignatureCredential;
use PayPal\Core\PPConstants;
use PayPal\Core\PPCredentialManager;
use PayPal\Exception\PPConfigurationException;

/**
 *
 * Adds non-authentication headers that are specific to
 * PayPal's Merchant APIs and determines endpoint to
 * hit based on configuration parameters.
 *
 */
class PPMerchantServiceHandler extends PPGenericServiceHandler
{
    public function __construct(private $apiUsername, $sdkName, $sdkVersion)
    {
        parent::__construct($sdkName, $sdkVersion);
    }

    #[Override]
    public function handle($httpConfig, $request, $options)
    {
        parent::handle($httpConfig, $request, $options);
        $config = $options['config'];

        if (is_string($this->apiUsername) || is_null($this->apiUsername)) {
            // $apiUsername is optional, if null the default account in config file is taken
            $credMgr = PPCredentialManager::getInstance($options['config']);
            $request->setCredential(clone($credMgr->getCredentialObject($this->apiUsername)));
        } else {
            $request->setCredential($this->apiUsername);
        }

        $endpoint   = '';
        $credential = $request->getCredential();
        if (isset($options['port'], $config['service.EndPoint.' . $options['port']])    ) {
            $endpoint = $config['service.EndPoint.' . $options['port']];
        } // for backward compatibilty (for those who are using old config files with 'service.EndPoint')
        elseif (isset($config['service.EndPoint'])) {
            $endpoint = $config['service.EndPoint'];
        } elseif (isset($config['mode'])) {
            if (strtoupper((string) $config['mode']) == 'SANDBOX') {
                if ($credential instanceof PPSignatureCredential) {
                    $endpoint = PPConstants::MERCHANT_SANDBOX_SIGNATURE_ENDPOINT;
                } elseif ($credential instanceof PPCertificateCredential) {
                    $endpoint = PPConstants::MERCHANT_SANDBOX_CERT_ENDPOINT;
                }
            } elseif (strtoupper((string) $config['mode']) == 'LIVE') {
                if ($credential instanceof PPSignatureCredential) {
                    $endpoint = PPConstants::MERCHANT_LIVE_SIGNATURE_ENDPOINT;
                } elseif ($credential instanceof PPCertificateCredential) {
                    $endpoint = PPConstants::MERCHANT_LIVE_CERT_ENDPOINT;
                }
            } elseif (strtoupper((string) $config['mode']) == 'TLS') {
                if ($credential instanceof PPSignatureCredential) {
                    $endpoint = PPConstants::MERCHANT_TLS_SIGNATURE_ENDPOINT;
                } elseif ($credential instanceof PPCertificateCredential) {
                    $endpoint = PPConstants::MERCHANT_TLS_CERT_ENDPOINT;
                }
            }
        } else {
            throw new PPConfigurationException('endpoint Not Set');
        }

        if ($request->getBindingType() == 'SOAP') {
            $httpConfig->setUrl($endpoint);
        } else {
            throw new PPConfigurationException('expecting service binding to be SOAP');
        }

        $request->addBindingInfo(
            "namespace",
            "xmlns:ns=\"urn:ebay:api:PayPalAPI\" xmlns:ebl=\"urn:ebay:apis:eBLBaseComponents\" xmlns:cc=\"urn:ebay:apis:CoreComponentTypes\" xmlns:ed=\"urn:ebay:apis:EnhancedDataTypes\""
        );
        // Call the authentication handler to tack authentication related info
        $handler = new PPAuthenticationHandler();
        $handler->handle($httpConfig, $request, $options);
    }
}
