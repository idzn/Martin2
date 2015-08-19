<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\components\Secure;

use Martin\exceptions\RuntimeError;

class Secure
{
    public $csrfToken;

    public function __construct($config)
    {
        if (!isset($_SESSION)) session_start();
        if (!isset($config['csrf_token']) || $config['csrf_token'] == '') {
            throw new RuntimeError('Please, set "csrf_token" option in config file. For example: f4PoaJd7a3mK3HB2mldIs');
        }
        $this->csrfToken = $config['csrf_token'];
    }

    public function csrfProtectHere()
    {
        echo '<input type="hidden" name="csrf_token" value="' . password_hash($this->csrfToken, PASSWORD_DEFAULT) . '"/>';
    }

    public function validCsrfToken()
    {
        return (password_verify($this->csrfToken, $_POST['csrf_token'])) ? true : false;
    }

    public function getRandomString($length = 20)
    {
        $signs = '`1234567890-=~!@#$%^&*()_+qwertyuiop[]\\asdfghjkl;\'zxcvbnm,./QWERTYUIOP{}|ASDFGHJKL:"ZXCVBNM<>?';
        $res = '';
        for ($i = 0; $i < $length; $i++) {
            $res .= $signs{mt_rand(0, strlen($signs)-1)};
        }
        return $res;
    }

}