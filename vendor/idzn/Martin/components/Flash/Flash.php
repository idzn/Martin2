<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\components\Flash;

class Flash
{
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';
    const TYPE_ERROR = 'danger';

    public function __construct($config)
    {
        if (!isset($_SESSION)) session_start();
    }

    public function exists($name)
    {
        return (isset($_SESSION['flash'][$name])) ? true : false;
    }

    public function set($name, $value, $class = 'info')
    {
        $_SESSION['flash'][$name] = ['message' => $value, 'class' => $class];
    }

    public function get($name)
    {
        return $_SESSION['flash'][$name];
    }

    public function renderMessagesHere()
    {
        if (!isset($_SESSION['flash'])) return;
        foreach ($_SESSION['flash'] as $flash) {
            echo '<div class="alert alert-' . $flash['class'] . ' alert-dismissable">';
            echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
            echo $flash['message'];
            echo '</div>';
        }
        $this->clear();
    }

    public function clear()
    {
        if (isset($_SESSION['flash'])) unset($_SESSION['flash']);
    }

}