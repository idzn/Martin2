<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\core;

use \Martin\core\exceptions\RuntimeError;
use Martin\core\traits\ErrorsTrait;
use Martin\core\traits\UrlMethodsTrait;

class Controller
{
    use ErrorsTrait;
    use UrlMethodsTrait;

    public $layout = 'default';

    public function __construct()
    {

    }

    public function __destruct()
    {

    }

    public function text($text, $status = '200 OK'){
        header("HTTP/1.x $status");
        return $text;
    }

    public function json($json, $status = '200 OK', $jsonParam = JSON_PRETTY_PRINT)
    {
        $this->text(json_encode($json, $jsonParam), $status);
    }

    public function render($view, $data = [])
    {
        $viewNamespacedClass = '\\' . Components::runtime()->moduleName . '\\extended\\' . 'View';
        ob_start();
        echo (new $viewNamespacedClass)->render($view, $data);
        $renderedView = ob_get_clean();


        $layoutArray = explode('/' , $this->layout);
        $layoutArray = array_map(function($value){
            return trim(strtolower($value));
        }, $layoutArray);
        if (count($layoutArray) == 1) {
            $layoutModule = Components::runtime()->moduleName;
            $layoutName = $this->layout;
        } elseif (count($layoutArray) == 2) {
            $layoutModule = $layoutArray[0];
            $layoutName = $layoutArray[1];
        }

        $layoutFile = constant(strtoupper($layoutModule) . '_LAYOUTS_PATH') .
            DIRECTORY_SEPARATOR .
            $layoutName .
            '.layout.php';
        if (!file_exists($layoutFile)) throw new RuntimeError('Layout file not found');

        ob_start();
        require $layoutFile;
        $layout = ob_get_clean();
        return $layout;
    }

    public function renderPartial($view, $data = [])
    {
        $viewNamespacedClass = '\\' . Components::runtime()->moduleName . '\\extended\\' . 'View';
        ob_start();
        echo (new $viewNamespacedClass())->render($view, $data);
        return ob_get_clean();
    }

    public function redirectTo($url, $status = 302)
    {
        header('Location: ' . $url, null, $status);
        exit;
    }

    public function redirectBack() {
        $this->redirectTo($_SERVER['HTTP_REFERER']);
    }

    function isAjax() {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;
    }

} 