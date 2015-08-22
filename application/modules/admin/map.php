<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

define('ADMIN_CONFIG_PATH', __DIR__ . '/config');
define('ADMIN_CONTROLLERS_PATH', __DIR__ . '/controllers');
define('ADMIN_MODELS_PATH', __DIR__ . '/models');
define('ADMIN_LAYOUTS_PATH', __DIR__ . '/layouts');
define('ADMIN_VIEWS_PATH', __DIR__ . '/views');

return array_replace_recursive(require ADMIN_CONFIG_PATH . DIRECTORY_SEPARATOR . 'pro.config.php',
    require ADMIN_CONFIG_PATH . DIRECTORY_SEPARATOR . APP_ENVIRONMENT . '.config.php');



 