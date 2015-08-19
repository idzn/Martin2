<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\core;


class Components
{
    /**
     * @return \Martin\components\Runtime\Runtime
     */
    public static function runtime()
    {
        return Container::get('runtime', '\Martin\components\Runtime\Runtime');
    }

    /**
     * @return \Martin\components\Debugger\Debugger
     */
    public static function debugger()
    {
        return Container::get('debugger', '\Martin\components\Debugger\Debugger');
    }

    /**
     * @return \Martin\components\Db\Db
     */
    public static function db()
    {
        return Container::get('db', '\Martin\components\Db\Db');
    }


    /**
     * @return \Martin\components\DbTables\DbTables
     */
    public static function dbTables()
    {
        return Container::get('dbTables', '\Martin\components\DbTables\DbTables');
    }

    /**
     * @return \Martin\components\User\User
     */
    public static function user()
    {
        return Container::get('user', '\Martin\components\User\User');
    }

    /**
     * @return \Martin\components\Flash\Flash
     */
    public static function flash()
    {
        return Container::get('flash', '\Martin\components\Flash\Flash');
    }

    /**
     * @return \Martin\components\Secure\Secure
     */
    public static function secure()
    {
        return Container::get('secure', '\Martin\components\Secure\Secure');
    }

    /**
     * @return \Martin\components\Validator\Validator
     */
    public static function validator()
    {
        return Container::get('validator', '\Martin\components\Validator\Validator');
    }

    /**
     * @return \Martin\components\Pager\Pager
     */
    public static function pager()
    {
        return Container::get('pager', '\Martin\components\Pager\Pager');
    }

    /**
     * @return \Martin\components\Html\Html
     */
    public static function html()
    {
        return Container::get('html', '\Martin\components\Html\Html');
    }

    /**
     * @return \Martin\components\Text\Text
     */
    public static function text()
    {
        return Container::get('text', '\Martin\components\Text\Text');
    }

    /**
     * @return \Martin\components\Assets\Assets
     */
    public static function assets()
    {
        return Container::get('assets', '\Martin\components\Assets\Assets');
    }
} 