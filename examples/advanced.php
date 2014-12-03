<?php

require "../vendor/autoload.php";

use \Pkj\Router\Router,
    \Pkj\Router\Route;



class BaseController {
    protected $router;
    public function setRouter (\Pkj\Router\Router $router) {
        $this->router = $router;
    }
}


class SomeController extends BaseController{
    public function home () {
        echo "Advanced: Hello World";
    }

    public function blog () {

    }
}



$router = new Router(\Pkj\Router\Request::bindFromHttpFactory());


// Read in all the routes.
$router->readFile(__DIR__ . '/routes.txt');


// This allows us to create custom factory for all controllers.
// Here we use the BaseController.
$router->setControllerFactory(function ($callable, $router) {
    if (is_array($callable)) {
        $controller = new $callable[0];
        if ($controller instanceof BaseController) {
            $controller->setRouter($router);
        }
        // Redefine $callable.
        $callable = array($controller, $callable[1]);
    }
    return $callable;
});



if ($executable = $router->run()) {
    $executable->execute();
} else {
    http_send_status(404);
    echo "<h1>404</h1>";
}


