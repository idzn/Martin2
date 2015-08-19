<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_STRICT);

return [
    'components' => [
        'db' => [
            /*
             * SQLite3
             */
            'dsn' => 'sqlite:' . DB_PATH . '/db.db',
            'user' => null,
            'pass' => null,
        ],
        'debugger' => [
            'enabled' => 1,
            'visibled' => 1,
        ],
    ]
];


