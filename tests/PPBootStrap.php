<?php
/**
 * Please run composer update from the root folder to run test cases
 */
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    define('PP_CONFIG_PATH', __DIR__);
    require __DIR__ . '/../vendor/autoload.php';
}
require __DIR__ . '/Constants.php';
