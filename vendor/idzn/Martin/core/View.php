<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\core;


use Martin\core\exceptions\RuntimeError;
use Martin\core\traits\ErrorsTrait;
use Martin\core\traits\UrlMethodsTrait;


class View
{
    use ErrorsTrait;
    use UrlMethodsTrait;


    public function __construct()
    {}

    public function __destruct()
    {}

    public function render($view, $data = [])
    {
        $viewArray = explode('/', $view);
        $viewArray = array_map(function($value){
            return trim(strtolower($value));
        }, $viewArray);

        if (count($viewArray) == 2) {
            $viewModule = Components::runtime()->moduleName;
            $viewLocation = implode(DIRECTORY_SEPARATOR, $viewArray);
        } elseif (count($viewArray) == 3) {
            $viewModule = $viewArray[0];
            $viewLocation = implode(DIRECTORY_SEPARATOR, [$viewArray[1], $viewArray[2]]);
        }

        $viewFile = constant(strtoupper($viewModule) . '_VIEWS_PATH') .
            DIRECTORY_SEPARATOR .
            $viewLocation .
            '.view.php';
        if (!file_exists($viewFile)) throw new RuntimeError('View file not found');
        ob_start();
        extract($data);
        require $viewFile;
        return ob_get_clean();
    }



} 