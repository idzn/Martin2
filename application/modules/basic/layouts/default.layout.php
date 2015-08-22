<?php
/**
 * @link https://github.com/idzn/Martin2
 * @copyright Copyright (c) 2015, Sergei Tolokonnikov
 * @license https://github.com/idzn/Martin2/blob/master/LICENSE
 */
use extended\Components;

/**
 * @var basic\extended\Controller $this
 */

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?=Components::runtime()->pageTitle?></title>
</head>
<body>
    <h1>this is basic layout</h1>
    <?=$renderedView?>
</body>
</html>