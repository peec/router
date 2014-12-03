<?php
/**
 * Created by PhpStorm.
 * User: pk
 * Date: 03.12.14
 * Time: 16:42
 */

namespace Pkj\Router;


class RouteStringLineException extends \Exception{

    public function __construct($file, $line, $message) {
        parent::__construct("Error: Route configuration in $file (Line $line): $message");
    }

} 