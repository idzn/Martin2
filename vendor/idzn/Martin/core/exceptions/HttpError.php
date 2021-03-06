<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */

namespace Martin\core\exceptions;

class HttpError extends \Exception
{
    public function __construct($httpStatusCode)
    {
        parent::__construct('', $httpStatusCode);
    }
}