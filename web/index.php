<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

$startTime = microtime(true);

use Martin\core\Application;

define('ROOT_PATH', __DIR__ . '/..');
define('VENDOR_PATH', __DIR__ . '/../vendor');
define('APPLICATION_PATH', __DIR__ . '/../application');
define('MODULES_PATH', APPLICATION_PATH . '/modules');
define('CONFIG_PATH', __DIR__ . '/../application/config');
define('DB_PATH', __DIR__ . '/../application/db');

spl_autoload_register(function($class) {
    $inVendorPath = VENDOR_PATH . '/idzn/' . str_ireplace('\\', '/', $class) . '.php';
    $inRootPath = ROOT_PATH . '/' . str_ireplace('\\', '/', $class) . '.php';
    $inApplicationPath = APPLICATION_PATH . '/' . str_ireplace('\\', '/', $class) . '.php';
    $inModulesPath = APPLICATION_PATH . '/modules/' . str_ireplace('\\', '/', $class) . '.php';
    file_exists($inVendorPath) and include_once $inVendorPath;
    file_exists($inRootPath) and include_once $inRootPath;
    file_exists($inApplicationPath) and include_once $inApplicationPath;
    file_exists($inModulesPath) and include_once $inModulesPath;
});

require CONFIG_PATH . DIRECTORY_SEPARATOR . 'environment.php';
$config = array_replace_recursive(require CONFIG_PATH . DIRECTORY_SEPARATOR . 'pro.config.php',
    require CONFIG_PATH . DIRECTORY_SEPARATOR . APP_ENVIRONMENT . '.config.php');

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

(new Application($config))->run();

echo '<hr>';echo sprintf('%0.5f',($startTime - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000) . ' ms' . ' | ' . round(memory_get_peak_usage()  / 1024, 2) . " KB";