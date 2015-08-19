<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\components\User;

class User
{
    public function __construct($config)
    {
        if (!isset($_SESSION)) session_start();
    }

    public function isAuthorized()
    {
        return (isset($_SESSION['user'])) ? true : false;
    }

    public function setArray($array)
    {
        $_SESSION['user'] = $array;
    }

    public function set($key, $value)
    {
        $_SESSION['user'][$key] = $value;
    }

    public function get($key = null)
    {
        return ($key === null) ? $_SESSION['user'] : $_SESSION['user'][$key];
    }

    public function forget()
    {
        unset($_SESSION['user']);
    }

}