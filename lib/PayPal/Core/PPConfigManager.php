<?php

namespace PayPal\Core;

/**
 * PPConfigManager loads the SDK configuration file and
 * hands out appropriate config params to other classes
 */
class PPConfigManager
{
    private $config;

    //default config values
    public static $defaults = ["http.ConnectionTimeOut" => "30", "http.TimeOut"           => "60", "http.Retry"             => "5"];

    /**
     * @var PPConfigManager
     */
    private static $instance;

    private function __construct()
    {
        $configFile = null;
        if (defined('PP_CONFIG_PATH')) {
            // if PP_CONFIG_PATH *is set* but not set to a string with length > 0
            // then let's disable ini file loading
            if (is_string(PP_CONFIG_PATH) && strlen(PP_CONFIG_PATH) > 0) {
                $configFile = PP_CONFIG_PATH . DIRECTORY_SEPARATOR . 'sdk_config.ini';
            }
        } else {
            $configFile = implode(
                DIRECTORY_SEPARATOR,
                [__DIR__, "..", "config", "sdk_config.ini"]
            );
        }
        if (!is_null($configFile) && file_exists($configFile)) {
            $this->load($configFile);
        }
    }

    // create singleton object for PPConfigManager
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new PPConfigManager();
        }

        return self::$instance;
    }

    //used to load the file
    private function load($fileName)
    {
        //Gracefully check for ini file
        $parsedConfig = @parse_ini_file($fileName);
        if (!empty($parsedConfig)) {
            $this->config = $parsedConfig;
        } else {
            $this->config = [];
        }
    }

    /**
     * simple getter for configuration params
     * If an exact match for key is not found,
     * does a "contains" search on the key
     *
     * @param mixed $searchKey
     */
    public function get($searchKey)
    {

        if (array_key_exists($searchKey, $this->config)) {
            return $this->config[$searchKey];
        }
        $arr = [];
        foreach ($this->config as $k => $v) {
            if (strstr((string) $k, (string) $searchKey)) {
                $arr[$k] = $v;
            }
        }

        return $arr;

    }

    /**
     * Utility method for handling account configuration
     * return config key corresponding to the API userId passed in
     *
     * If $userId is null, returns config keys corresponding to
     * all configured accounts
     *
     * @param null|mixed $userId
     */
    public function getIniPrefix($userId = null)
    {

        if ($userId == null) {
            $arr = [];
            foreach ($this->config as $key => $value) {
                $pos = strpos((string) $key, '.');
                if (strstr((string) $key, "acct")) {
                    $arr[] = substr((string) $key, 0, $pos);
                }
            }

            return array_unique($arr);
        }
        $iniPrefix = array_search($userId, $this->config);
        $pos       = strpos((string) $iniPrefix, '.');
        $acct      = substr((string) $iniPrefix, 0, $pos);

        return $acct;

    }

    /**
     * returns the config file hashmap
     *
     */
    private function getConfigHashmap()
    {
        return $this->config;
    }

    /**
     * use  the default configuration if it is not passed in hashmap
     *
     * @param null|mixed $config
     */
    public static function getConfigWithDefaults($config = null)
    {
        if (!is_array(PPConfigManager::getInstance()->getConfigHashmap()) && $config == null) {
            return PPConfigManager::$defaults;
        }

        return array_merge(
            PPConfigManager::$defaults,
            ($config != null) ? $config : PPConfigManager::getInstance()->getConfigHashmap()
        );
    }
}
