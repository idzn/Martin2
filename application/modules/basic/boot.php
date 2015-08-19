<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

define('BASIC_CONFIG_PATH', __DIR__ . '/config');
define('BASIC_CONTROLLERS_PATH', __DIR__ . '/controllers');
define('BASIC_MODELS_PATH', __DIR__ . '/models');
define('BASIC_LAYOUTS_PATH', __DIR__ . '/layouts');
define('BASIC_VIEWS_PATH', __DIR__ . '/views');

return array_replace_recursive(require BASIC_CONFIG_PATH . DIRECTORY_SEPARATOR . 'pro.config.php',
    require BASIC_CONFIG_PATH . DIRECTORY_SEPARATOR . APP_ENVIRONMENT . '.config.php');

