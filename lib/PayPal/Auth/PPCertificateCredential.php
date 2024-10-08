<?php

namespace PayPal\Auth;

use Override;
use PayPal\Exception\PPMissingCredentialException;

/**
 *
 * Client certificate based credentials
 */
class PPCertificateCredential extends IPPCredential
{
    /**
     * API username
     *
     * @var string
     */
    protected $userName;

    /**
     * API password
     *
     * @var string
     */
    protected $password;

    /**
     * Path to PEM encoded API certificate on local filesystem
     *
     * @var string
     */
    protected $certificatePath;

    /**
     * Application Id that uniquely identifies an application that uses the
     * Platform APIs - Not required for Express Checkout / MassPay / DCC etc
     * The application Id is issued by PayPal.
     * Test application Ids are available for the sandbox environment
     *
     * @var string
     */
    protected $applicationId;

    /**
     * Constructs a new certificate credential object
     *
     * @param string $userName              API username
     * @param string $password              API password
     * @param string $certPath              Path to PEM encoded client certificate file
     * @param string $certificatePassPhrase password need to use the certificate
     */
    public function __construct($userName, $password, $certPath, /**
     * Password used to protect the API certificate
     */
        protected $certificatePassPhrase = null)
    {
        $this->userName              = trim($userName);
        $this->password              = trim($password);
        $this->certificatePath       = trim($certPath);
        $this->validate();
    }

    #[Override]
    public function validate()
    {

        if (empty($this->userName)) {
            throw new PPMissingCredentialException("username cannot be empty");
        }
        if (empty($this->password)) {
            throw new PPMissingCredentialException("password cannot be empty");
        }
        if (empty($this->certificatePath)) {
            throw new PPMissingCredentialException("certificate cannot be empty");
        }
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getCertificatePath()
    {
        if (realpath($this->certificatePath)) {
            return realpath($this->certificatePath);
        } elseif (defined('PP_CONFIG_PATH')) {
            return constant('PP_CONFIG_PATH') . DIRECTORY_SEPARATOR . $this->certificatePath;
        }

        return realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . $this->certificatePath);

    }

    public function getCertificatePassPhrase()
    {
        return $this->certificatePassPhrase;
    }

    public function setApplicationId($applicationId)
    {
        $this->applicationId = trim((string) $applicationId);
    }

    public function getApplicationId()
    {
        return $this->applicationId;
    }
}
