<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

ini_set('display_errors', false);
error_reporting(0);

return [
    'app' => [
        'name' => 'Martin',
        'protocol' => 'http',
        'host' => 'martin',
    ],
    'routing' => [
        'routes' => [
            'home' => ['get', '/', 'basic/main/index'],
            'admin_home' => ['get', '/admin', 'admin/main/index'],
        ],
        'patterns' => [
            '{str}' => '[a-zA-Z]+',
            '{int}' => '[\d]+',
            '{any}' => '[^\/]+',
            '{:-)}' => '[^\/]+',
        ],
    ],
    'components' => [
        'db' => [
            'errMode' => 'exception', // silent / warning / exception
            'tablePrefix' => '',
            'persistent' => true,
            /*
             * MySQL
             */
            'dsn' => 'mysql:host=localhost;dbname=martin;charset=utf8',
            'user' => 'root',
            'pass' => 'qwerty123',
        ],
        'user' => [],
        'flash' => [],
        'secure' => [
            'csrf_token' => 'f4PoaJd7a3mK3HB2mldIs', // must be not empty!
        ],
        'pager' => [],
        'debugger' => [
            'enabled' => true,
            'visibled' => true,
        ],
        'assets' => [
            'pathMode' => 0777,
        ],
    ]
];



 