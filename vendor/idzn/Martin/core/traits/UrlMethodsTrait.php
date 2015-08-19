<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\core\traits;

use Martin\core\Components;

trait UrlMethodsTrait
{
    public function urlFor($routeName, $params = [])
    {
        foreach (Components::runtime()->config['routing']['routes'] as $routeKey => $route) {

            if ($routeKey != $routeName) continue;
            foreach (Components::runtime()->config['routing']['patterns'] as $pattern => $regex) {
                $route[1] = str_ireplace($pattern, '{}', $route[1]);
            }
            preg_match_all('|{}|', $route[1], $matches);
            if (count($params) != count($matches[0])) return;
            $matches[0] = array_map(function($val){ return '|' . $val . '|'; }, $matches[0]);
            return Components::runtime()->config['app']['protocol'] . '://' . Components::runtime()->config['app']['host'] . preg_replace($matches[0], $params, $route[1], 1);
        }
        return;
    }
}