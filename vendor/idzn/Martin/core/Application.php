<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\core;

use Martin\core\exceptions\HttpError;
use Martin\core\exceptions\RuntimeError;
use Martin\core\traits\ErrorsTrait;

class Application
{
    use ErrorsTrait;

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function __destruct()
    {}

    private function routing($routes, $patterns)
    {
        $requestInfo = [];
        foreach ($routes as $routeName => list($methods, $url, $goal)) {

            $methods = array_map('strtoupper', array_map('trim', explode(',', $methods)));

            $uriArray = explode('?', $_SERVER['REQUEST_URI']);
            if (count($uriArray) > 2) throw new HttpError(404);
            $uri = $uriArray[0];
            if (isset($uriArray[1])) {
                parse_str($uriArray[1], $_GET);
            }

            foreach ($patterns as $patternKey => $pattern) {
                $url = str_replace($patternKey, '(' . $pattern . ')', $url);
            }

            if (!preg_match_all(
                '|^('. $url .')$|i',
                $uri,
                $matches,
                PREG_SET_ORDER)
            ) continue;

            $goalArray = explode('/', $goal);
            if (count($goalArray) != 3) throw new RuntimeError('wrong route goal');
            $goalArray = array_map('trim', $goalArray);

            array_shift($matches[0]) && array_shift($matches[0]);

            $requestInfo = [
                'routeName' => $routeName,
                'routeMethods' => $methods,
                'uri' => $uri,
                'module' => $goalArray[0],
                'controller' => $goalArray[1],
                'action' => $goalArray[2],
                'params' => $matches[0],
            ];

        }
        return $requestInfo;
    }

    private function runController($requestInfo)
    {
        $controllerClassName = ucfirst($requestInfo['controller']) . 'Controller';
        $namespacedClassName = $requestInfo['module'] . '\\controllers\\' . $controllerClassName;
        $controllerFile = constant(strtoupper($requestInfo['module']) . '_CONTROLLERS_PATH') .
            DIRECTORY_SEPARATOR . $controllerClassName . '.php';

        if (!file_exists($controllerFile)) throw new RuntimeError('controller file not found');
        require $controllerFile;

        $controllerObject = new $namespacedClassName();
        if (!method_exists($controllerObject, 'action_' . $requestInfo['action'])) throw new RuntimeError('action not found');
        echo call_user_func_array([$controllerObject, 'action_' . $requestInfo['action']], $requestInfo['params']);
    }

    public function run()
    {
        try {
            $requestInfo = $this->routing(
                $this->config['routing']['routes'],
                $this->config['routing']['patterns']
            );
            if (empty($requestInfo)) throw new HttpError(404);
            if (!in_array($_SERVER['REQUEST_METHOD'], $requestInfo['routeMethods'])) throw new HttpError(405);

            $modulesDir = scandir(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules');
            array_shift($modulesDir) && array_shift($modulesDir);
            if (!in_array($requestInfo['module'], $modulesDir)) throw new RuntimeError('module "' .
                $requestInfo['module'] . '" not found');

            foreach ($modulesDir as $dir) {
                if ($dir == $requestInfo['module']) {
                    $moduleConfig = require MODULES_PATH . DIRECTORY_SEPARATOR . $requestInfo['module'] . '/boot.php';
                    $this->config = array_replace_recursive($this->config, $moduleConfig);
                }
            }


            new Container();

            $componentsNamespacedClass = '\\' . $requestInfo['module'] . '\\extended\\' . 'Components';
            new $componentsNamespacedClass();
            $componentsNamespacedClass::runtime()->config = $this->config;
            $this->config = null;

            $componentsNamespacedClass::runtime()->uri = $requestInfo['uri'];
            $componentsNamespacedClass::runtime()->routeName = $requestInfo['routeName'];
            $componentsNamespacedClass::runtime()->moduleName = $requestInfo['module'];
            $componentsNamespacedClass::runtime()->controllerName = $requestInfo['controller'];
            $componentsNamespacedClass::runtime()->actionName = $requestInfo['action'];
            $componentsNamespacedClass::runtime()->actionParams = $requestInfo['params'];

            $this->runController($requestInfo);
        }
        catch (HttpError $e) { $this->sendHttpError($e->getCode()); }
        catch (RuntimeError $e) { @ob_end_clean(); $this->showRuntimeError($e); exit; }
        finally {

        }

    }


}